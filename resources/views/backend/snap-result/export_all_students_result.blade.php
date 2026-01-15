<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>WhatsApp Number</th>
            <th>Category</th>
            <th>Overall Percentile</th>
            <th>English Max Score</th>
            <th>Logical Max Score</th>
            <th>Quantative Max Score</th>
            <th>Total Obtained Marks</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($results as $record)

            <tr>
                <td>{{ $record->snap_user->name ?? 'N/A' }}</td>
                <td>{{ $record->snap_user->email ?? 'N/A' }}</td>
                <td>{{ $record->snap_user->whatsapp_number ?? 'N/A' }}</td>

                <td>{{ $record->category ?? 'N/A' }}</td>
                <td>{{ $record->overall_percentile ?? 'N/A' }}</td>
                <td>{{ $record->english ?? 'N/A' }}</td>
                <td>{{ $record->logical ?? 'N/A' }}</td>
                <td>{{ $record->quant ?? 'N/A' }}</td>
                <td>{{ $record->max_score ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
