<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CAT Result Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; }
        h1 { text-align: center; color: #2b2b2b; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .section { margin-top: 30px; }
    </style>
</head>
<body>
    <h1>CAT Result Summary</h1>    

    <div class="section">
        <h3>Student Details</h3>
        <table>
            @foreach($result['details'] as $key => $value)
                <tr>
                    <th>{{ ucfirst($key) }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <p><strong>Percentile:</strong> {{ $percentile }}</p>

    <div class="section">
        <h3>Section-wise Marks</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Section Name</th>
                    <th>Total Marks</th>
                    <th>Obtained Marks</th>
                    <th>Total Questions</th>
                    <th>Attempted</th>
                    <th>Correct</th>
                    <th>Wrong</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($result['sections_marks'] as $section)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $section['name'] ?? '-' }}</td>
                        <td>{{ $section['total_marks'] ?? '-' }}</td>
                        <td>{{ $section['obtain_marks'] ?? '-' }}</td>
                        <td>{{ $section['total_questions'] ?? '-' }}</td>
                        <td>{{ $section['attempt_questions'] ?? '-' }}</td>
                        <td>{{ $section['correct_answers'] ?? '-' }}</td>
                        <td>{{ $section['wrong_answers'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Suggested Colleges</h3>
        <table>
            <thead>
                <tr>
                    <th>College Name</th>
                    <th>Percentile Range</th>
                    <th>Fees</th>
                    <th>Median CTC</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suggested as $college)
                    <tr>
                        <td>{{ $college->college_name }}</td>
                        <td>{{ $college->percentile_between }}</td>
                        <td>{{ $college->fees }}</td>
                        <td>{{ $college->average_package }}</td>
                        <td>{{ $college->deadline }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No suggested colleges found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
