<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Manager List</h3>
                    <a href="<?php echo base_url(); ?>manager/viewManagerAdd" class="btn btn-default pull pull-right">Add new manager</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Created_at (Local)</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>

<!-- DataTables -->
<script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- page script -->
<script>
    $(function() {
        var datatable = $('#example1').DataTable({
            "autoWidth": true,
            "width": '100%',
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "pageLength": 20,
            "order": [
                [0, "asc"]
            ],
            "ajax": {
                url: `${base_url}manager/getManagerData`,
                type: "POST",
                "dataSrc": function(json) {
                    return json.data;
                }
            },

            "columns": [{
                    'data': 'id'
                },
                {
                    'data': 'email'
                },
                {
                    'data': 'password'
                },
                {
                    'data': 'created_at'
                },
                {
                    'data': null,
                    className: 'text-center',
                },
            ],

            columnDefs: [
                {
                "width": "10%",
                "targets": 1,
                render: function(data, type, row) {
                    const linkurl = `${base_url}manager/viewEdit/${row.id}`;
                    const html = `<a href = "${linkurl}">${data}</a>`;
                    return html;
                    }
                }, 
                {
                    "width": "10%",
                    "targets": 3,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var date = new Date(data * 1000);
                        date_format = date.toLocaleString();
                        return date_format
                    }
                },
                {
                    "width": "10%",
                    "targets": 4,
                    "orderable": false,
                    className: 'delete',
                    render: function(data, type, row) {
                        const html = `<td class="delete"><i class="fa fa-trash fa-remove" data-id="${row.id}"></td>`;
                        return html;
                    }
                }, ],
        })

        $(document).on('click', 'i.fa-remove', function() {
            const id = $(this).data('id')
            var x = confirm("Are you sure you want to delete?")
            var url = base_url + 'manager/delete'
            if (x) {
                $.post(url, {
                    id: id
                }, function(data, status) {
                    datatable.ajax.reload(null, false);
                })
            } else {
                return false
            }
        });

    })
</script>