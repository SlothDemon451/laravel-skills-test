<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Product Form</h1>
        <form id="productForm">
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" class="form-control" id="productName" name="product_name" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity in Stock</label>
                <input type="number" class="form-control" id="quantity" name="quantity_in_stock" required>
            </div>
            <div class="form-group">
                <label for="price">Price per Item</label>
                <input type="number" class="form-control" id="price" name="price_per_item" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <h2 class="mt-5">Submitted Data</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Price per Item</th>
                    <th>Date Time Submitted</th>
                    <th>Total Value Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                @foreach($products as $product)
                    <tr data-id="{{ $product->id }}">
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->quantity_in_stock }}</td>
                        <td>{{ $product->price_per_item }}</td>
                        <td>{{ $product->created_at }}</td>
                        <td>{{ $product->quantity_in_stock * $product->price_per_item }}</td>
                        <td>
                            <button class="btn btn-warning btn-edit">Edit</button>
                            <button class="btn btn-danger btn-delete">Delete</button>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-right">Total</td>
                    <td id="totalValue"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editProductId">
                        <div class="form-group">
                            <label for="editProductName">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editQuantity">Quantity in Stock</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity_in_stock" required>
                        </div>
                        <div class="form-group">
                            <label for="editPrice">Price per Item</label>
                            <input type="number" class="form-control" id="editPrice" name="price_per_item" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateTotal() {
                let total = 0;
                $('#productTableBody tr').each(function() {
                    const value = parseFloat($(this).find('td').eq(4).text());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
                $('#totalValue').text(total.toFixed(2));
            }

                $(document).ready(function() {
                
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        
                $('#productForm').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '/store',
                        method: 'POST',
                        data: $(this).serialize() + '&_token=' + csrfToken,
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr.responseText);
                        }
                    });
                });
            });

            $('#productTableBody').on('click', '.btn-delete', function() {
                const row = $(this).closest('tr');
                const id = row.data('id');
                $.ajax({
                    url: `/delete/${id}`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        row.remove();
                        updateTotal();
                    }
                });
            });

            $('#productTableBody').on('click', '.btn-edit', function() {
                const row = $(this).closest('tr');
                const id = row.data('id');
                $.ajax({
                    url: `/edit/${id}`,
                    method: 'GET',
                    success: function(data) {
                        $('#editProductId').val(data.id);
                        $('#editProductName').val(data.product_name);
                        $('#editQuantity').val(data.quantity_in_stock);
                        $('#editPrice').val(data.price_per_item);
                        $('#editModal').modal('show');
                    }
                });
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                const id = $('#editProductId').val();
                $.ajax({
                    url: `/update/${id}`,
                    method: 'POST',
                    data: $(this).serialize() + '&_token={{ csrf_token() }}',
                    success: function(response) {
                        location.reload();
                    }
                });
            });

            updateTotal();
        });
    </script>
</body>
</html>
