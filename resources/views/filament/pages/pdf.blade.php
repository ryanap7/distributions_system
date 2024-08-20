<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <style>
        @page {
            size: A4;
            margin-left: 8mm;
            margin-right: 20mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0;
            font-size: 20px;
        }

        .report-details {
            margin-bottom: 20px;
        }

        .report-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-details th,
        .report-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .report-details th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .report-details tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        footer {
            text-align: center;
            margin-top: 20px;
        }

        footer p {
            margin: 0;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Laporan</h1>
        </header>

        <div class="report-details">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kecamatan</th>
                        <th>Desa</th>
                        <th>Tahap</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($distributions as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->recipient->name }}</td>
                        <td>{{ $item->village->district->name }}</td>
                        <td>{{ $item->village->name }}</td>
                        <td>{{ $item->stage }}</td>
                        <td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</td>
                        <td>{{ $item->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <footer>
            <p>&copy; {{ date('Y') }} Sistem Pengamanan Bantuan Dana Desa. Semua hak cipta dilindungi.</p>
        </footer>
    </div>
</body>

</html>