<table>
		<thead>
			<th>claveSeccion</th>
			<th>claveSubmodulo</th>
			<th>descripcion</th>
			<th>constante</th>
			<th>Activo</th>
		</thead>
		<tbody>
			@foreach($lista as $permiso)
				<tr>
				<td>{{$permiso['claveSeccion']}}</td>
				<td>{{$permiso['claveSubmodulo']}}</td>
				<td>{{$permiso['descripcion']}}</td>
				<td>{{$permiso['constante']}}</td>
				<td>{{$permiso['Activo']}}</td>
				</tr>
			@endforeach
		</tbody>
</table>
{{dd($guardados)}}
{{dd($borrados)}}