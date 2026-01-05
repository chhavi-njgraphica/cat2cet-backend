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
                <td>{{ $result->total_marks ?? 'N/A' }}</td>
                <td>{{ $result->obtain_marks ?? 'N/A' }}</td>
            </tr>

            {{-- Section-wise marks --}}
            @if (!empty($result->sections_marks))
                <tr>
                    <td colspan="13" style="font-weight:bold; text-align:center;">
                        Section-wise Performance
                    </td>
                </tr>
                <tr>
                    <th>Sr No.</th>
                    <th>Section Name</th>
                    <th>Total Marks</th>
                    <th>Obtained Marks</th>
                    <th>Total Questions</th>
                    <th>Attempted</th>
                    <th>Correct</th>
                    <th>Wrong</th>
                    <th colspan="5"></th>
                </tr>
                @foreach ($result->sections_marks as $section)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $section->name }}</td>
                        <td>{{ $section->total_marks }}</td>
                        <td>{{ $section->obtain_marks }}</td>
                        <td>{{ $section->total_questions }}</td>
                        <td>{{ $section->attempt_questions }}</td>
                        <td>{{ $section->correct_answers }}</td>
                        <td>{{ $section->wrong_answers }}</td>
                        <td colspan="5"></td>
                    </tr>
                @endforeach

                @if (!empty($result->gk_section_marks))
                    @php $gk = $result->gk_section_marks; @endphp
                        <tr>
                            <td>4</td>
                            <td>{{ $gk->name }}</td>
                            <td>{{ $gk->obtain_marks }}</td>
                            <td>{{ $gk->total_questions }}</td>
                            <td>{{ $gk->attempt_questions }}</td>
                            <td>{{ $gk->correct_answers }}</td>
                            <td>{{ $gk->wrong_answers }}</td>
                        </tr>
                @endif
            @endif

            <tr><td colspan="13"></td></tr>
        @endforeach
    </tbody>
</table>
