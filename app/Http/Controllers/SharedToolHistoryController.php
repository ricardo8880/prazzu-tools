<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Tools\History\Services\ToolHistoryContextResolver;
use App\Core\Tools\History\Services\ToolHistoryExporter;
use App\Core\Tools\History\Services\ToolHistoryManager;
use App\Core\Tools\ToolRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class SharedToolHistoryController extends Controller
{
    public function index(Request $request, ToolHistoryManager $history, ToolRegistry $registry, ToolHistoryContextResolver $contexts): View
    {
        [$slug, $routePrefix] = $this->context($request);
        $page = $history->paginate($slug, (int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1)));

        return view('tools.shared.history.index', [
            'tool' => $registry->findManifest($slug),
            'routePrefix' => $routePrefix,
            'historyPage' => $page,
            'historyContexts' => collect($page->items)->mapWithKeys(
                static fn ($entry): array => [$entry->id => $contexts->resolveEntry($entry)],
            )->all(),
        ]);
    }

    public function show(Request $request, string $run, ToolHistoryManager $history, ToolRegistry $registry, ToolHistoryContextResolver $contexts): View
    {
        [$slug, $routePrefix] = $this->context($request);
        $entry = $history->find($slug, $run, (int) $request->user()->getAuthIdentifier());

        return view('tools.shared.history.show', [
            'tool' => $registry->findManifest($slug),
            'routePrefix' => $routePrefix,
            'entry' => $entry,
            'contextLabel' => $contexts->resolveEntry($entry),
        ]);
    }

    public function repeat(Request $request, string $run, ToolHistoryManager $history): RedirectResponse
    {
        [$slug, $routePrefix] = $this->context($request);
        $entry = $history->find($slug, $run, (int) $request->user()->getAuthIdentifier());
        $request->session()->flashInput($entry->input);

        return redirect()
            ->route($routePrefix.'.index')
            ->with('status', 'Dados carregados. Revise as premissas e calcule novamente com as regras atuais.');
    }

    public function duplicate(Request $request, string $run, ToolHistoryManager $history): RedirectResponse
    {
        [$slug, $routePrefix] = $this->context($request);
        $copy = $history->duplicate($slug, $run, (int) $request->user()->getAuthIdentifier());

        return redirect()
            ->route($routePrefix.'.history.show', ['run' => $copy->id])
            ->with('status', 'Cópia criada no histórico.');
    }

    public function destroy(Request $request, string $run, ToolHistoryManager $history): RedirectResponse
    {
        [$slug, $routePrefix] = $this->context($request);
        $history->delete($slug, $run, (int) $request->user()->getAuthIdentifier());

        return redirect()
            ->route($routePrefix.'.history.index')
            ->with('status', 'Registro excluído.');
    }

    public function export(
        Request $request,
        string $run,
        string $format,
        ToolHistoryManager $history,
        ToolHistoryExporter $exporter,
    ): View|Response|StreamedResponse {
        [$slug] = $this->context($request);
        $entry = $history->find($slug, $run, (int) $request->user()->getAuthIdentifier());

        return $exporter->export($entry, $format);
    }

    /**
     * @return array{string, string}
     */
    private function context(Request $request): array
    {
        $slug = $request->route('_tool_slug');
        $routePrefix = $request->route('_tool_route_prefix');

        if (! is_string($slug) || ! is_string($routePrefix) || $slug === '' || $routePrefix === '') {
            throw new InvalidArgumentException('Contexto da ferramenta não configurado.');
        }

        return [$slug, $routePrefix];
    }
}
