<!-- resources/views/admin/users/index.blade.php -->

<h1>スタッフ一覧</h1>

<table>
    <thead>
        <tr>
            <th>氏名</th>
            <th>メールアドレス</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <a href="{{ route('admin.attendance.staff', ['id' => $user->id]) }}">
    勤怠一覧
</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>