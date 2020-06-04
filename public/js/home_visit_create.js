function rowEntry(control) {
	alert(base);
	alert('oye');
	var id = $(control).closest('tr').find('td:first div').html();
	var url = base + '/home_visit/create/pasien/' + id;
	window.location = url;
}
