<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Camera List</h3>
                    <button class="btn btn-default pull pull-right" data-toggle="modal"
                    data-target="#modal-camera-add">Add new camera</button>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Camera Name</th>
                                <th>IMEI</th>
                                <th>Email(owner)</th>
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

    <div class="modal modal-primary fade" id="modal-camera-add">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add a camera</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-8">
                                    <input id="input_camera_name" type="text" class="form-control"
                                        placeholder="Camera's Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">IMEI</label>
                                <div class="col-sm-8">
                                    <input id="input_camera_IMEI" type="text" maxlength="15"
                                        oninput="validateInput(this)" class="form-control" placeholder="IMEI">
                                    <p id="error" style="color: white;"></p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline" id="button-add-camera">Add</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
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
                url: `${base_url}camera/getCameraData`,
                type: "POST",
                "dataSrc": function(json) {
                    return json.data;
                }
            },

            "columns": [{
                    'data': 'id'
                },
                {
                    'data': 'name'
                },
                {
                    'data': 'IMEI'
                },
                {
                    'data': 'user_email'
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
                    "targets": 4,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var date = new Date(data * 1000);
                        date_format = date.toLocaleString();
                        return date_format
                    }
                },
                {
                    "width": "10%",
                    "targets": 5,
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
            var url = base_url + 'camera/delete'
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

        $('#button-add-camera').click(function () {
            var camera_name = $("#input_camera_name").val()
            var camera_IMEI = $("#input_camera_IMEI").val()
            var url = base_url + 'camera/add_camera'
            $.post(url, {
                // user_id: user_id,
                camera_name: camera_name,
                camera_IMEI: camera_IMEI
            }, function (data, status) {
                var obj = JSON.parse(data);
                if (obj.result == 'ok') {
                    $("#input_camera_name").val('')
                    $("#input_camera_IMEI").val('')
                    $('#modal-camera-add').modal('hide');
                    datatable.ajax.reload(null, false);
                } else {
                    alert(obj.message);
                }
            })
            return false;
        });

    })
</script>