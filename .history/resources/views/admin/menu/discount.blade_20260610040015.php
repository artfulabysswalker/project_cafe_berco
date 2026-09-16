display
<form method="POST" action="{{ route('admin.menu.discount.store', $menu->id_menu) }}">
    @csrf

    <label>Discount Price</label>
    <input type="number" name="discount_price" required>

    <label>Duration</label>
    <select name="duration" required>
        <option value="7">1 Week</option>
        <option value="30">1 Month</option>
    </select>

    <button type="submit">Apply Discount</button>
</form>