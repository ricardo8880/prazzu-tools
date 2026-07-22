<?php

namespace App\Http\Controllers\Admin\Acquisition;

use App\Core\Acquisition\Application\Admin\DeleteAcquisitionContext;
use App\Core\Acquisition\Application\Admin\GetAcquisitionContextForm;
use App\Core\Acquisition\Application\Admin\ListAcquisitionContexts;
use App\Core\Acquisition\Application\Admin\SaveAcquisitionContext;
use App\Core\Acquisition\Application\Admin\ToggleAcquisitionContext;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Acquisition\SaveAcquisitionContextRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AcquisitionContextController extends Controller
{
    public function index(Request $request, ListAcquisitionContexts $action): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        return view('admin.acquisition.index', [
            'contexts' => $action->execute($search, $status),
            'search' => $search,
            'selectedStatus' => $status,
            'statuses' => AcquisitionContextStatus::cases(),
        ]);
    }

    public function create(GetAcquisitionContextForm $action): View
    {
        return view('admin.acquisition.create', $action->execute());
    }

    public function store(SaveAcquisitionContextRequest $request, SaveAcquisitionContext $action): RedirectResponse
    {
        $id = $action->execute(null, $request->validated());

        return redirect()
            ->route('admin.acquisition.contexts.edit', $id)
            ->with('status', 'Contexto de aquisição criado com sucesso.');
    }

    public function edit(int $context, GetAcquisitionContextForm $action): View
    {
        $data = $action->execute($context);

        if ($data['context'] === null) {
            throw new NotFoundHttpException;
        }

        return view('admin.acquisition.edit', $data);
    }

    public function update(
        SaveAcquisitionContextRequest $request,
        int $context,
        SaveAcquisitionContext $action,
    ): RedirectResponse {
        $action->execute($context, $request->validated());

        return redirect()
            ->route('admin.acquisition.contexts.edit', $context)
            ->with('status', 'Contexto de aquisição atualizado com sucesso.');
    }

    public function toggle(int $context, ToggleAcquisitionContext $action): RedirectResponse
    {
        $active = $action->execute($context);

        return back()->with('status', $active
            ? 'Contexto ativado com sucesso.'
            : 'Contexto desativado com sucesso.');
    }

    public function destroy(int $context, DeleteAcquisitionContext $action): RedirectResponse
    {
        $action->execute($context);

        return redirect()
            ->route('admin.acquisition.contexts.index')
            ->with('status', 'Contexto de aquisição excluído com sucesso.');
    }
}
