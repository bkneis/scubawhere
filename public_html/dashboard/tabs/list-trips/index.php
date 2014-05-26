<div id="wrapper">
	<table>
		<caption></caption>
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Duration</th>
			</tr>
		</thead>
		
		<tbody id="trips">
			<script id="trip" type="text/x-handlebars-template">
						
					<tr>
						<td>{{name}}</td>
						<td>{{{description}}}</td>
						<td>{{duration}} Hours</td>
					</tr>
				
			</script>
		</tbody>
	</table>
		
</div>

<script src="tabs/list-trips/js/script.js"></script>
