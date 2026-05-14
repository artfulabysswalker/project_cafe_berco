@extends('dashboard')

@section('content')

<h1 style="margin-bottom:20px;">Orders</h1>

<table width="100%" border="1" cellspacing="0" cellpadding="10">
    <tr style="background:#f4f4f4;">
        <th>Order ID</th>
        <th>Customer</th>
        <th>Status</th>
        <th>Date</th>
    </tr>

    <!-- Dummy Data -->
    <tr>
        <td>#1001</td>
        <td>John Doe</td>
        <td style="color:orange;">Pending</td>
        <td>2026-04-01</td>
    </tr>

    <tr>
        <td>#1002</td>
        <td>Jane Smith</td>
        <td style="color:green;">Completed</td>
        <td>2026-04-02</td>
    </tr>

    <tr>
        <td>#1003</td>
        <td>Mike Johnson</td>
        <td style="color:red;">Cancelled</td>
        <td>2026-04-03</td>
    </tr>

</table>

<!-- 🔷 Fake Buttons -->
<div style="margin-top:20px;">
    <button style="padding:10px; background:#3498db; color:white; border:none; border-radius:5px;">
        Add Order
    </button>

    <button style="padding:10px; background:#2ecc71; color:white; border:none; border-radius:5px;">
        Export
    </button>
</div>