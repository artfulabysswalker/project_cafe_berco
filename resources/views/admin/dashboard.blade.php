<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cafe Berco</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div style="padding: 20px;">
        <h1 style="text-align: center; margin-bottom: 30px;">Admin Dashboard - Cafe Berco</h1>

        <div style="display: flex; justify-content: space-around; margin-bottom: 30px;">
            <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; text-align: center;">
                <h2>Total Orders</h2>
                <p style="font-size: 2em; font-weight: bold;">{{ $totalOrders }}</p>
            </div>

            <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; text-align: center;">
                <h2>Total Revenue</h2>
                <p style="font-size: 2em; font-weight: bold;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>

            <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; text-align: center;">
                <h2>Products Sold</h2>
                <p style="font-size: 2em; font-weight: bold;">{{ $totalProductsSold }}</p>
            </div>
        </div>

        <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px;">
            <h2>Top 5 Products</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #ccc;">
                        <th style="text-align: left; padding: 8px;">Product Name</th>
                        <th style="text-align: left; padding: 8px;">Total Sold</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $item)
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td style="padding: 8px;">{{ $item->product->name ?? 'Unknown' }}</td>
                            <td style="padding: 8px;">{{ $item->total_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>