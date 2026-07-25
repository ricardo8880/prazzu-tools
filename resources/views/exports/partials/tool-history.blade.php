<section>
    <h2>Entradas</h2>
    <table>
        <tbody>
            @foreach ($entry->input as $key => $value)
                <tr>
                    <th>{{ \Illuminate\Support\Str::headline((string) $key) }}</th>
                    <td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

<section>
    <h2>Resultados</h2>
    <table>
        <tbody>
            @foreach ($entry->result as $key => $value)
                @continue(in_array($key, ['summary', 'details', 'warnings', 'next_actions'], true))
                <tr>
                    <th>{{ \Illuminate\Support\Str::headline((string) $key) }}</th>
                    <td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
