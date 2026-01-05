<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>WhatsApp Number</th>
            <th>Candidate Name</th>
            <th>Application No</th>
            <th>Test Center Name</th>
            <th>Percentile</th>
            <th>Total Marks</th>
            <th>Obtained Marks</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($results as $record)
            @php $result = $record->decoded_data; @endphp

            <tr>
                <td>{{ $record->user->name ?? 'N/A' }}</td>
                <td>{{ $record->user->email ?? 'N/A' }}</td>
                <td>{{ $record->user->whatsapp_number ?? 'N/A' }}</td>

                <td>{{ $result->details->{'Candidate Name'} ?? 'N/A' }}</td>
                <td>{{ $result->details->{'XAT ID'} ?? 'N/A' }}</td>
                <td>{{ $result->details->{'TC Name'} ?? 'N/A' }}</td>

                <td>{{ $result->percentile ?? 'N/A' }}</td>

                {{-- Show total and obtained marks here --}}
                <td>{{ $result->total_marks ?? 'N/A' }}</td>
                <td>{{ $result->obtain_marks ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
