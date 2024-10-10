@if(!count($arrayObjects))
<p>{{__('No se encontraron productos.')}}</p>
@else
<table class="table table--w-100 table--hover table--sticky-header">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Precio</th>
            <th>Fecha de creación</th>
            @if($isAuthenticated)
                <th></th>
            @endif
        </tr>
    </thead>
    @foreach($arrayObjects as $product)
    <tr>
        <td>{{$product->id}}</td>
        <td>{{$product->title}}</td>
        <td>$ @money($product->price)</td>
        <td>{{$product->created_at}}</td>
        @if($isAuthenticated)
            <td>
                <button class="btn btn-primary btn-sm edit-button" data-product="{{ json_encode($product) }}">Editar</button>
                <button class="btn btn-danger btn-sm delete-button" data-id="{{ $product->id }}">Borrar</button>
            </td>
        @endif
        </td>
    </tr>
    @endforeach
</table>

{{$pagination->links()}}

@endif