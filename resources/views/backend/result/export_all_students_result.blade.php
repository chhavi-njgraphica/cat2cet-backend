<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>WhatsApp Number</th>
            <th>Candidate Name</th>
            <th>Application No</th>
            <th>Subject</th>
            <th>Test Center Name</th>
            <th>Shift</th>
            <th>Test Date</th>
            <th>Test Time</th>
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
                <td>{{ $result->details->{'Application No'} ?? 'N/A' }}</td>
                <td>{{ $result->details->Subject ?? 'N/A' }}</td>
                <td>{{ $result->details->{'Test Center Name'} ?? 'N/A' }}</td>
                <td>{{ $result->details->Shift ?? 'N/A' }}</td>
                <td>{{ $result->details->{'Test Date'} ?? 'N/A' }}</td>
                <td>{{ $result->details->{'Test Time'} ?? 'N/A' }}</td>

                <td>{{ $result->percentile ?? 'N/A' }}</td>

                {{-- Show total and obtained marks here --}}
                <td>{{ $result->total_marks ?? 'N/A' }}</td>
                <td>{{ $result->obtain_marks ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
