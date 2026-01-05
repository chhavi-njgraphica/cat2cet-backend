<table border="1">
    <thead>
        <tr>
            <th>Sr No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Whatsapp Number</th>
        </tr>
    </thead>
    <tbody>
                
        @foreach ($results as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->whatsapp_number }}</td>
                <td colspan="5"></td>
            </tr>
        @endforeach
    </tbody>
</table>
