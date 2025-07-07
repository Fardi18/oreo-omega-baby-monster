<table>
    <thead>
    <tr>
        <th>Content</th>
    </tr>
    </thead>
    <tbody>
        @if (isset($data[0]))
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->content }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>