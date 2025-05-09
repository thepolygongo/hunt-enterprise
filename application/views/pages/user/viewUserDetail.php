<style>
    .example-modal .modal {
        position: relative;
        top: auto;
        bottom: auto;
        right: auto;
        left: auto;
        display: block;
        z-index: 1;
    }

    .example-modal .modal {
        background: transparent !important;
    }

    fieldset {
        border: 1px solid silver;
        margin: 0 2px;
        padding: 0.35em 0.625em 0.75em;

    }

    legend {
        font-size: 14px;
    }

    .data_usage_input {
        text-align: right;
    }
</style>
<link rel="icon" href="<?php echo base_url(); ?>/assets/images/hunt_control_logo.png" type="image/png">
<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet"
    href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet"
    href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<!-- Select2 -->
<link rel="stylesheet"
    href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/select2/dist/css/select2.min.css">

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>    
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

<!-- InputMask -->
<script src="<?php echo base_url(); ?>assets/AdminLTE/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url(); ?>assets/AdminLTE/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/AdminLTE/plugins/input-mask/jquery.inputmask.extensions.js"></script>

<!-- Select2 -->
<script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/select2/dist/js/select2.full.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">User Info</h3>
                    <button id="btn_goto_member" type="button" class=" pull-right"><i
                            class="fa fa-hand-o-right"></i>&nbsp;Go Member Site</button>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" style="text-align:center">Email</label>
                            <div class="col-sm-5">
                                <input id="email" type="email" class="form-control" placeholder="Email"
                                    value="<?php echo $user['email']; ?>" readonly>
                            </div>                                
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" style="text-align:center">Password</label>
                            <div class="col-sm-5">
                                <input id="resetPassword" type="text" class="form-control" placeholder="<?php echo $user['password']; ?>">
                            </div>
                            <div class="col-sm-3 text-center">
                                <button type="button" id="buttonReset" class="btn btn-default">Reset</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <button type="button" id="buttonForceLogout" class="btn btn-default">Force
                                    Logout</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Sub Accounts</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tableSub" class="table table-bordered table-striped" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>email</th>
                                <th>customer_id</th>
                                <th>created_at</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="button" id="buttonRemoveSub" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-sub-add">
                        Add
                    </button>
                </div>
                <!-- /.box-footer -->
            </div>
            <!-- TO DO List -->
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Customer Notes</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- See dist/js/pages/dashboard.js to activate the todoList plugin -->
                    <ul id="list_todo" class="todo-list">
                    </ul>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix no-border">
                    <button type="button" class="btn btn-default pull-right todo-add"><i class="fa fa-plus"></i> Add
                        item</button>
                </div>
            </div>
            <!-- /.box -->
            <!-- general form elements -->
            <!-- <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Notes</h3>
                </div>
                <div class="box-body">
                    <table id="tableSubscriptionNotes" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>author</th>
                                <th>note</th>
                                <th>customer_note</th>
                                <th>created_at</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> -->
        </div>
        <!-- /.box -->
        <!-- left column -->
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title" id="cameras_title">Cameras</h3>
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-toggle="modal"
                            data-target="#modal-camera-assign">
                            Assign Camera
                        </button>
                        <!-- <button type="button" id="buttonLabelCameras" class="btn btn-default">Label Cameras</button> -->
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tableCamera" class="table table-bordered table-striped" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>Type</th>
                                <th>Carrier</th>
                                <th>IMEI</th>
                                <th>iccid</th>
                                <th>Activate</th>
                                <th>Usage</th>
                                <th>created_at</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Daily Report</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- title row -->
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <select class="form-control" id="smart_camera">
                            </select>
                        </div>
                    </div>
                    <br />
                    <!-- title row -->
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Last Updated:</b> <span id="last_updated"></span><br>
                            <b>Firmware Version:</b> <span id="firmware_version"></span><br>
                            <b>SD Card:</b> <span id="sd_card"></span><br>
                            <b>Battery:</b> <span id="battery"></span><br>
                            <b>Signal:</b> <span id="signal"></span><br>
                        </div>
                        <div class="col-sm-8 invoice-col">
                            <table id="tableDaily">
                            </table>
                            <!-- <b>Camera Mode:</b> <span id="camera_mode"></span><br>
                            <b>Burst Mode:</b> <span id="burst_mode"></span><br>
                            <b>Motion Sensitivity:</b> <span id="motion_sensitivity"></span><br>
                            <b>Picture Size:</b> <span id="picture_size"></span><br>
                            <b>Camera Check In:</b> <span id="camera_check_in"></span><br>
                            <b>Sending Mode:</b> <span id="sending_mode"></span><br>
                            <b>Night Mode:</b> <span id="night_mode"></span><br> -->
                        </div>
                        <!-- <div class="col-sm-4 invoice-col">
                            <b>IR Flash:</b> <span id="ir_flash"></span><br>
                            <b>Delay:</b> <span id="delay"></span><br>
                            <b>Camera Name:</b> <span id="camera_name"></span><br>
                            <b>Time Lapse:</b> <span id="time_lapse"></span><br>
                            <b>Work Timer1:</b> <span id="work_timer1"></span><br>
                            <b>Work Timer2:</b> <span id="work_timer2"></span><br>
                        </div> -->
                    </div>
                </div>
            </div>
            <!-- AREA CHART -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Data Usage</h3>
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" id="btn-datausage-reset">
                            Datausage Reset
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="usageTotal" style="height:250px"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Data Alert</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-5 control-label" style="text-align:center">Data threshold</label>
                        <div class="col-sm-3">
                            <input id="dataThreshold" type="text" class="form-control" placeholder=""
                                value="<?php echo '500'; ?>">
                        </div>
                        <div class="col-sm-4 text-center">
                            <button type="button" id="buttonThreshold" class="btn btn-default">Set</button>
                        </div>                            
                    </div>
                    <div class="chart">
                        <canvas id="monitorUsage" style="height:250px"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    <div class="modal modal-primary fade" id="modal-sub-add">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add new sub account</h4>
                </div>
                <div class="modal-body">
                    <p>Please select an user</p>
                    <select class="form-control" id="select-add-user">
                        <?php
                        foreach ($users as $user_item) {
                            echo "<option value='" . $user_item['id'] . "'>" . $user_item['email'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline" id="buttonAddSub">Add</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal modal-primary fade" id="modal-camera-assign" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign a camera</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Cameras</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="camera_list">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline" id="button-assign-camera">Assign</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</section>




<script>
    function validateInput(input) {
        let errorMessage = document.getElementById("error");

        // Remove non-numeric characters
        input.value = input.value.replace(/\D/g, '');

        // Check if the length is exactly 15
        if (input.value.length > 15) {
            input.value = input.value.slice(0, 15);
        }

        if (input.value.length < 15) {
            errorMessage.textContent = "Please enter exactly 15 digits.";
        } else {
            errorMessage.textContent = "";
        }
    }
    $('[data-mask]').inputmask()
    var user_id = "<?php echo $user['id']; ?>"

    const format = 'HH:mm';

    const optionsCameraMode = [{
        value: 1,
        label: 'Photo (default)'
    },
    {
        value: 2,
        label: 'Video'
    },
    {
        value: 3,
        label: 'PIC+Video'
    },
    ];

    const optionsMultiShot_big = [{
        value: 1,
        label: '1 Picture (default)'
    },
    {
        value: 2,
        label: '2 Pictures'
    },
    {
        value: 3,
        label: '3 Pictures'
    },
    {
        value: 4,
        label: '4 Pictures'
    },
    {
        value: 5,
        label: '5 Pictures'
    },
    ];

    const optionsPictureSize_big = [{
        value: 3,
        label: '5M (default)'
    },
    {
        value: 2,
        label: '8M'
    },
    {
        value: 1,
        label: '12M'
    },
    ];

    const optionsPirSensitivity_big = [{
        value: 0,
        label: 'High (default)'
    },
    {
        value: 1,
        label: 'Middle'
    },
    {
        value: 2,
        label: 'Low'
    },
    ];

    const optionsCellular_big = [{
        value: 0,
        label: 'Once a Day (default)'
    },
    {
        value: 1,
        label: 'Always Available'
    },
    ];

    const optionsNightMode_big = [{
        value: 1,
        label: 'Max.Range'
    },
    {
        value: 2,
        label: 'Balanced (default)'
    },
    {
        value: 3,
        label: 'Min.Blur'
    },
    ];

    const optionsPictureSize = [{
        value: 3,
        label: '5M (default)'
    },
    {
        value: 2,
        label: '8M'
    },
    {
        value: 1,
        label: '12M'
    },
    ];

    const optionsPirSensitivity = [{
        value: 1,
        label: '1 (Lowest)'
    },
    {
        value: 2,
        label: '2'
    },
    {
        value: 3,
        label: '3'
    },
    {
        value: 4,
        label: '4'
    },
    {
        value: 5,
        label: '5'
    },
    {
        value: 6,
        label: '6'
    },
    {
        value: 7,
        label: '7 (Default)'
    },
    {
        value: 8,
        label: '8'
    },
    {
        value: 9,
        label: '9 (Highest)'
    },
    ];

    const optionsCellular = [{
        value: 0,
        label: 'Once/4 Times a Day (default)'
    },
    {
        value: 1,
        label: 'Always Available'
    },
    ];

    const optionsMultiShot = [{
        value: 1,
        label: '1 Picture (default)'
    },
    {
        value: 2,
        label: '2 Pictures'
    },
    {
        value: 3,
        label: '3 Pictures'
    },
    ];

    const optionsNightMode = [{
        value: 1,
        label: 'Max.Range'
    },
    {
        value: 2,
        label: 'Balanced (default)'
    },
    {
        value: 3,
        label: 'Min.Blur'
    },
    ];

    const optionsSendingMode = [{
        value: 0,
        label: 'Instant(default)'
    },
    {
        value: 1,
        label: 'Every 1H'
    },
    {
        value: 2,
        label: 'Every 4H'
    },
    ];

    const optionsIRFlash = [{
        value: 0,
        label: 'Far (default)'
    },
    {
        value: 1,
        label: 'Near'
    },
    ];

    const optionsVideoSending = [{
        value: 0,
        label: 'Don’t Send Video',
        disabled: true
    },
    {
        value: 1,
        label: 'Thumbnail Image'
    },
    {
        value: 2,
        label: 'Full Video (default)'
    },
    ];

    const optionsVideoSending_dc2 = [{
        value: 0,
        label: 'OFF',
        disabled: true
    }, {
        value: 1,
        label: 'Thumbnail Video',
        disabled: true
    },
    {
        value: 2,
        label: 'Full Video (default)'
    },
    {
        value: 3,
        label: 'Thumbnail Image'
    },
    ];

    const optionsVideoSize = [{
        value: 1,
        label: '1080P'
    },
    {
        value: 2,
        label: '720P'
    },
    {
        value: 3,
        label: 'WVGA (default)'
    },
    ];

    const optionsVideoLength = [{
        value: 5,
        label: '5  (default)'
    },
    {
        value: 10,
        label: '10'
    },
    {
        value: 15,
        label: '15'
    },
    ];

    const getLabel = (value, options) => {
        for (let i = 0; i < options.length; i++) {
            if (parseInt(value, 10) == options[i].value) {
                return options[i].label;
            }
        }
        return '';
    };

    function savePassword() {
        var newPassword1 = document.getElementById('newPassword1').value
        var newPassword2 = document.getElementById('newPassword2').value
        if (newPassword1 == "" || newPassword1 != newPassword2) {
            alert("Please input valid data.");
        } else {

            $.post('<?php echo base_url() ?>user/changePassword', {
                id: user_id,
                password: newPassword1,
            }, function (data, status) {
                if (data == 'success') {
                    $('#modal-primary').modal('hide');
                    location.reload();
                } else {
                    alert(data);
                }
            });
            return false;
        }
    }

    function usageString(data) {
        var dataString = "0B"
        if (data > 1000000000) {
            var value = (data / 1000000000).toFixed(1)
            dataString = value + "GB"
        } else if (data > 1000000) {
            var value = (data / 1000000).toFixed(1)
            dataString = value + "MB"
        } else if (data > 1000) {
            var value = (data / 1000).toFixed(1)
            dataString = value + "KB"
        } else if (data > 1) {
            var value = data
            dataString = value + "B"
        }
        return dataString;
    }

    $(function () {
        //--------------
        //- PIE CHART -
        //--------------
        var initPieChart = {
            // The type of chart we want to create
            type: 'pie',

            // The data for our dataset
            data: {
                datasets: [{
                    data: [10, 20],
                    backgroundColor: [
                        colorRed_alpha,
                        colorDeer_alpha,
                    ],
                    borderColor: [
                        colorRed,
                        colorDeer,
                    ],
                    borderWidth: 2
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    'Usage',
                    'Free',
                ],
            },
        };

        var myPieChart = new Chart($('#usageTotal'), initPieChart);

        function load_usage_total() {
            var url = base_url + "/user/get_total_usage";
            $.getJSON(url, {
                "user_id": user_id
            }, function (result) {
                usage_limitation = result.usage_limitation;
                current_usage = result.current_usage;
                if (usage_limitation < 0)
                    usage_limitation = current_usage * 10
                free_usage = usage_limitation - current_usage;
                if (free_usage < 0)
                    free_usage = 0;
                myPieChart.data.datasets[0].data = [current_usage, free_usage];
                // myPieChart.data.datasets[0].data = [1, 2];
                myPieChart.data.labels = ['Current Usage-' + usageString(current_usage), 'Free Usage-' + (result.usage_limitation == -1 ? "Unlimited" : usageString(usage_limitation - current_usage))]
                myPieChart.update();
            });
        }

        load_usage_total()

        var initLineChart = {
            type: 'line',

            data: {
                datasets: [{
                    label: "Time-Datausage",
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: colorDeer_alpha,
                    borderColor: colorDeer,
                    borderWidth: 2,
                }],
            },

            // Configuration options go here
            options: {
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true // minimum value will be 0.
                        }
                    }]
                }
            }
        };

        var monitorUsageChart = new Chart($('#monitorUsage'), initLineChart);

        function load_data_usage() {
            var url = base_url + "/user/get_total_usage";
            $.getJSON(url, {
                "user_id": user_id
            }, function (result) {
                usage_limitation = result.usage_limitation;
                current_usage = result.current_usage;
                if (usage_limitation < 0)
                    usage_limitation = current_usage * 10
                free_usage = usage_limitation - current_usage;
                if (free_usage < 0)
                    free_usage = 0;
                monitorUsageChart.data.datasets[0].data = [current_usage, free_usage];
                // monitorUsageChart.data.datasets[0].data = [1, 2];
                monitorUsageChart.data.labels = ['Current Usage-' + usageString(current_usage), 'Free Usage-' + (result.usage_limitation == -1 ? "Unlimited" : usageString(usage_limitation - current_usage))]
                monitorUsageChart.update();
            });
        }

        load_data_usage()


        $('#btn-datausage-reset').click(function () {
            var url = base_url + 'user/datausage_reset'
            $.post(url, {
                user_id: user_id,
            }, function (data, status) {
                load_usage_total()
                tableCamera.ajax.reload(null, false);
            })
        });

        //Initialize Select2 Elements
        $('.select2').select2()

        var tableCamera = $('#tableCamera').DataTable({
            "autoWidth": true,
            "width": '100%',
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "pageLength": 10,
            searching: false,
            "order": [
                [0, "asc"]
            ],
            "ajax": {
                url: `${base_url}camera/getListByUser`,
                data: function (d) {
                    Object.assign(d, {
                        "user_id": user_id,
                    });
                    return d;
                },
                type: "POST",
                "dataSrc": function (json) {
                    var total_usage = 0;
                    for (var i = 0; i < json.data.length; i++) {
                        if (json.data[i].data_usage > 1)
                            total_usage += parseInt(json.data[i].data_usage, 10);
                    }

                    $("#cameras_title").text("Cameras - Data Usage: " + usageString(total_usage));
                    return json.data;
                }
            },

            "columns": [{
                'data': 'id',
                className: 'text-center',
            },
            {
                'data': 'name',
                className: 'text-center',
            },
            {
                'data': 'version',
                className: 'text-center',
            },
            {
                'data': 'att_verizon',
                className: 'text-center',
            },
            {
                'data': 'IMEI',
                className: 'text-center',
            },
            {
                'data': 'iccid',
                className: 'text-center',
            },
            {
                'data': 'is_active',
                className: 'text-center',
            },
            {
                'data': 'data_usage',
                className: 'text-center',
            },
            {
                'data': 'created_at',
                className: 'text-center',
            },
            ],

            columnDefs: [{
                "width": "10%",
                "targets": 2,
                render: function (data, type, row) {
                    var strData = data;
                    if (data == "A") {
                        strData = "Standard"
                    } else if (data == "B") {
                        strData = "Mini"
                    } else if (data == "C") {
                        strData = "DataCam"
                    }
                    return strData;
                }
            }, {
                "width": "10%",
                "targets": 3,
                render: function (data, type, row) {
                    return data;
                }
            },
            {
                "width": "10%",
                "targets": 4,
                render: function (data, type, row) {
                    if (row.IMEI != "") {
                        const html = '<a type="button" class="fa fa-edit edit-IMEI"  data-imei="' + row.IMEI + '" data-id="' + row.id + '">' + data + '</a>';
                        return html;
                    }
                    return "";
                }
            },
            {
                "width": "10%",
                "targets": 5,
                render: function (data, type, row) {
                    return data
                }
            },
            {
                "width": "10%",
                "targets": 6,
                render: function (data, type, row) {
                    if (row.IMEI != "") {
                        if (data == 0) {
                            return '<button class="btn btn-danger btn-activate" data-imei="' + row.IMEI + '">Suspended</button>';
                        }
                        return '<button class="btn btn-success btn-deactivate" data-imei="' + row.IMEI + '">Activated</button>';
                    }
                    return "";
                }
            },
            {
                "width": "10%",
                "targets": 7,
                "orderable": false,
                render: function (data, type, row) {
                    return usageString(data);
                }
            },
            {
                "targets": 9,
                "orderable": false,
                render: function (data, type, row) {
                    if (row.IMEI != "") {
                        const linkurl = `${base_url}camera/viewEdit/${row.device_id}`;
                        const html = `<i class="fa fa-edit" data-link = "${linkurl}"></i>`;
                        return html;
                    }
                    return "";
                }
            },
            {
                "targets": 10,
                "orderable": false,
                render: function (data, type, row) {
                    const html = `<td class="delete"><i class="fa fa-trash fa-remove" data-id="${row.id}"></td>`;
                    return html;
                }
            },
            ],
        })


        $(document).on('click', '.edit-IMEI', function () {
            const camera_id = $(this).data('id')
            const IMEI = $(this).data('imei')
            var newIMEI = prompt("Please enter new IMEI:", IMEI);

            if (newIMEI == null) { } else {
                var url = base_url + 'camera/updateIMEI'
                $.post(url, {
                    user_id: user_id,
                    camera_id: camera_id,
                    IMEI: IMEI,
                    newIMEI: newIMEI
                }, function (data, status) {
                    if (data == 'ok') {
                        tableCamera.ajax.reload(null, false);
                        load_todo_list()
                    } else {
                        alert(data);
                    }
                })
            }
        });

        $(document).on('click', '.btn-activate', function () {
            const IMEI = $(this).data('imei')

            var x = confirm("Are you sure you want to reactivate?")
            if (x) {
                var url = base_url + 'camera/activate'
                $.post(url, {
                    IMEI: IMEI,
                    is_active: 1
                }, function (data, status) {
                    var obj = JSON.parse(data);
                    if (obj.result == 'OK') {
                        tableCamera.ajax.reload(null, false);
                    } else {
                        alert(data);
                    }
                })
            }
        });

        $(document).on('click', '.btn-deactivate', function () {
            const IMEI = $(this).data('imei')

            var x = confirm("Are you sure you want to suspend?")
            if (x) {
                var url = base_url + 'camera/activate'
                $.post(url, {
                    IMEI: IMEI,
                    is_active: 0
                }, function (data, status) {
                    var obj = JSON.parse(data);
                    if (obj.result == 'OK') {
                        tableCamera.ajax.reload(null, false);
                    } else {
                        alert(data);
                    }
                })
            }
        });

        $('#btn_goto_member').click(function () {
            token = encodeURIComponent("<?php echo $user['token']; ?>")
            if (token == '') {
                var url = base_url + 'user/create_token'
                $.post(url, {
                    user_id: user_id,
                }, function (data, status) {
                    token = data
                    url = "<?php echo URL_MEMBER_SITE; ?>signInToken/" + token;
                    var win = window.open(encodeURI(url), '_blank');
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Please allow popups for this website');
                    }
                })
            } else {
                url = "<?php echo URL_MEMBER_SITE; ?>signInToken/" + token;
                var win = window.open(encodeURI(url), '_blank');
                if (win) {
                    //Browser has allowed it to be opened
                    win.focus();
                } else {
                    //Browser has blocked it
                    alert('Please allow popups for this website');
                }
            }
        });

        $('#buttonPriceSave').click(function () {
            inpObj = document.getElementById('price_per_cam')
            if (!inpObj.checkValidity()) {
                inpObj.reportValidity();
                return false;
            } else { }
            inpObj = document.getElementById('price_per_GB')
            if (!inpObj.checkValidity()) {
                inpObj.reportValidity();
                return false;
            } else { }
            price_per_cam = $('#price_per_cam').val();
            price_per_GB = $("#price_per_GB").val();
            var url = base_url + 'user/ranch_price_update'
            $.post(url, {
                user_id: user_id,
                price_per_cam: price_per_cam,
                price_per_GB: price_per_GB,
            }, function (data, status) {
                // location.reload();
                alert("Saved");
            })
        });

        $('#checkbox-special').change(function () {
            var special = this.checked;
            var url = base_url + 'user/update_special'
            $.post(url, {
                user_id: user_id,
                special: special
            }, function (data, status) {
                location.reload();
            })
        });

        $('#is_main').change(function () {
            http_save_account()
        });

        function http_save_account() {
            name = document.getElementById('name').value;
            email = document.getElementById('email').value;
            account_type = document.getElementById('account_type').value;
            is_main = document.getElementById('is_main').value;

            var url = base_url + 'user/update_hunt_info'
            $.post(url, {
                user_id: user_id,
                name: name,
                email: email,
                account_type: account_type,
                is_main: is_main,
            }, function (data, status) {
                location.reload()
            })
        }

        

        var url = base_url + 'user/smart_cams'
        $.getJSON(url, {
            user_id: user_id,
        }, function (json, status) {
            $('#smart_camera').empty();
            for (i = 0; i < json.length; ++i) {
                $('#smart_camera').append('<option value="' + json[i].IMEI + '">' + json[i].name + " (" + json[i].IMEI + ')</option>');
            }
            if (json.length > 0) {
                http_load_daily_report();
            }
        })

        $('#smart_camera').on('change', function () {
            http_load_daily_report();
        });

        $('#smart_camera').on('change', function () {
            http_load_daily_report();
        });


        $('#account_type').on('change', function () {
            account_type = document.getElementById('account_type').value;

            var url = base_url + 'user/update_account_type'
            $.post(url, {
                user_id: user_id,
                account_type: account_type,
            }, function (data, status) {
                location.reload()
            })
        });

        function http_load_daily_report() {
            var IMEI = $('#smart_camera').val();
            $.post('<?php echo base_url() ?>camera/device_setting_get', {
                IMEI: IMEI,
            }, function (data, status) {
                var obj = JSON.parse(data);
                if (obj.result == "ERROR")
                    return;
                device = obj.device
                setting = obj.setting

                if (device.version == 'MC2' || device.version == 'DC2B') {
                    var sd_string = (device.sd_card / 1000).toFixed(1) + "GB/" + (device.sd_card_max / 1000).toFixed(1) + "GB"
                } else {
                    var sd_string = (device.sd_card_max - device.sd_card) + "GB/" + device.sd_card_max + "GB"
                }
                if (device.last_text_uploaded_at > 1000) {
                    var date = new Date(device.last_text_uploaded_at * 1000);
                    date_format = date.toLocaleString();
                    $("#last_updated").html(date_format)
                } else
                    $("#last_updated").html('')
                $("#firmware_version").html(device.firmware_version)
                $("#sd_card").html(sd_string)

                if (device.battery1 != "") {
                    var battery = device.battery2 + "/" + device.battery1
                    $("#battery").html(battery)
                } else {
                    $("#battery").html(device.battery)
                }

                if (device.csq_percent > 0) {
                    $("#signal").html(device.csq_percent + '%')
                } else {

                    if (device.version == 'B') {
                        $("#signal").html((device.csq * 25 > 100 ? 100 : device.csq * 25) + '%')
                    } else {
                        $("#signal").html(device.csq + '%')
                    }
                }

                var index = 0;
                var html = ""
                for (const [key_obj, value_obj] of Object.entries(setting)) {
                    var key = key_obj;
                    var value = value_obj;
                    if (device.version == 'A') {
                        if (key == "camera_mode") {
                            value = getLabel(value, optionsCameraMode);
                        } else if (key == "multi_shot") {
                            key = 'burst_mode';
                            value = getLabel(value, optionsMultiShot_big);
                        } else if (key == "pir_sensitivity") {
                            key = 'motion_sensitivity';
                            value = getLabel(value, optionsPirSensitivity_big);
                        } else if (key == "picture_size") {
                            value = getLabel(value, optionsPictureSize_big);
                        } else if (key == "sms_remote") {
                            value = getLabel(value, optionsCellular_big);
                        } else if (key == "night_mode") {
                            value = getLabel(value, optionsNightMode_big);
                        } else if (key == "delay") { } else if (key == "time_lapse") { } else if (key == "work_timer1") { } else if (key == "work_timer2") { } else {
                            // continue;
                        }
                    } else if (device.version == 'B' || device.version == 'C' || device.version == 'DC2') {
                        if (key == "camera_mode") {
                            value = getLabel(value, optionsCameraMode);
                        } else if (key == "multi_shot") {
                            key = 'burst_mode';
                            value = getLabel(value, optionsMultiShot);
                        } else if (key == "pir_sensitivity") {
                            key = 'motion_sensitivity';
                            value = getLabel(value, optionsPirSensitivity);
                        } else if (key == "picture_size") {
                            value = getLabel(value, optionsPictureSize);
                        } else if (key == "sms_remote") {
                            value = getLabel(value, optionsCellular);
                        } else if (key == "night_mode") {
                            value = getLabel(value, optionsNightMode);
                        } else if (key == "ir_flash") {
                            value = getLabel(value, optionsIRFlash);
                        } else if (key == "trans_video") {
                            key = 'video_option'
                            if (device.version == 'DC2')
                                value = getLabel(value, optionsVideoSending_dc2);
                            else
                                value = getLabel(value, optionsVideoSending);
                        } else if (key == "video_quality") {
                            value = getLabel(value, optionsVideoSize);
                        } else if (key == "video_length") {
                            value = getLabel(value, optionsVideoLength);
                        } else if (key == "sending_mode") {
                            value = getLabel(value, optionsSendingMode);
                        } else if (key == "delay") { } else if (key == "time_lapse") { } else if (key == "work_timer1") { } else if (key == "work_timer2") { } else {
                            // continue;
                        }
                    }

                    if ((index % 2) == 0)
                        html += "<tr><td><b>" + key + "</b>: <span>" + value + "  </span></td>"
                    else
                        html += "<td><b>" + key + "</b>:<span>" + value + "  </span></td></tr>"
                    index = index + 1;

                }
                $("#tableDaily").html(html);
            });
        }
        http_load_daily_report();

        // sub accounts table
        var tableSub = $('#tableSub').DataTable({
            "autoWidth": true,
            "width": '100%',
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "pageLength": 10,
            searching: false,
            "order": [
                [0, "asc"]
            ],
            "select": true,
            "bPaginate": false,
            "ajax": {
                url: `${base_url}user/getSubAccounts`,
                data: function (d) {
                    Object.assign(d, {
                        "user_id": user_id,
                    });
                    return d;
                },
                type: "POST",
                "dataSrc": function (json) {
                    var return_data = new Array();
                    for (var i = 0; i < json.data.length; i++) {
                        return_data.push({
                            'id': json.data[i].id,
                            'name': json.data[i].name,
                            'email': json.data[i].email,
                            'customer_id': json.data[i].customer_id,
                            'created_at': json.data[i].created_at,
                        })
                    }
                    return return_data;
                }
            },

            "columns": [{
                'data': 'id'
            },
            {
                'data': 'name'
            },
            {
                'data': 'email'
            },
            {
                'data': 'customer_id'
            },
            {
                'data': 'created_at'
            },
            ],
        })

        $('#buttonRemoveSub').click(function () {
            var items = [];
            var rows = tableSub.rows({
                selected: true
            });
            var datas = rows.data();
            for (var i = 0; i < datas.length; i++) {
                data = datas[i];
                items.push(data.id);
            }
            if (items.length > 0) {
                var url = base_url + 'user/remove_subs'
                $.post(url, {
                    user_id: user_id,
                    ids: items
                }, function (data, status) {
                    tableSub.ajax.reload(null, false);
                })
            }
        });

        $('#buttonAddSub').click(function () {
            var sub_id = document.getElementById('select-add-user').value
            $.post('<?php echo base_url() ?>user/add_sub', {
                main_id: user_id,
                sub_id: sub_id,
            }, function (data, status) {
                if (data == 'success') {
                    $('#modal-sub-add').modal('hide');
                    tableSub.ajax.reload(null, false);
                } else {
                    alert(data);
                }
            });
            return false;
        });

        // customer notes
        function load_todo_list() {
            var url = base_url + 'user/list_todo'
            $.getJSON(url, {
                user_id: user_id,
            }, function (json, status) {
                $('#list_todo').empty();
                for (i = 0; i < json.length; ++i) {
                    var date = new Date(json[i].created_at * 1000);
                    date_format = date.toLocaleString();
                    $html = "<li><input class = 'todo_check' type='checkbox' value=''  " + (json[i].checked == 1 ? "checked" : "") + " data-id='" + json[i].created_at + "'><span class='text text-muted'>" + date_format + "</span><span class='text'>" + json[i].note + "</span><span class='text text-muted'>" + json[i].by_admin + "</span><div class='tools'><i class='fa fa-trash-o todo-remove' data-id='" + json[i].created_at + "'></i></div></li>";
                    $('#list_todo').append($html);
                }
            })
        }
        load_todo_list()

        $(document).on('click', '.todo-remove', function () {
            const created_at = $(this).data('id')
            $.post('<?php echo base_url() ?>user/todo_remove', {
                user_id: user_id,
                created_at: created_at,
            }, function (data, status) {
                load_todo_list()
            });
            return false;
        });

        $(document).on('click', '.todo-edit', function () {
            const id = $(this).data('id')
            const note = $(this).data('note')
            let new_note = prompt('Type here', note);
            if (new_note != null && new_note != "") {
                $.post('<?php echo base_url() ?>user/todo_edit', {
                    id: id,
                    note: new_note,
                }, function (data, status) {
                    load_todo_list()
                });
            }
            return false;
        });

        $(document).on('click', '.todo-add', function () {
            let note = prompt('Type here');
            if (note != null && note != "") {
                $.post('<?php echo base_url() ?>user/todo_add', {
                    user_id: user_id,
                    note: note,
                }, function (data, status) {
                    load_todo_list()
                });
            }
            return false;
        });

        $(document).on('click', '.todo_check', function () {
            const created_at = $(this).data('id')
            const value = $(this).is(":checked") ? 1 : 0

            $.post('<?php echo base_url() ?>user/todo_check', {
                created_at: created_at,
                user_id: user_id,
                checked: value,
            }, function (data, status) {
                load_todo_list()
            });
        });

        $('#buttonForceLogout').click(function () {
            var url = base_url + 'user/force_logout'
            $.post(url, {
                user_id: user_id,
            }, function (data, status) {
                // alert(data);
                location.reload();
            })
        });

        token = "<?php echo $user['token']; ?>"
        if (token == '' || token == null) {
            $('#buttonForceLogout').hide();
        }

        $('#buttonReset').click(function () {
            var url = base_url + 'user/reset_password'
            $.post(url, {
                user_id: user_id,
                password: $('#resetPassword').val()
            }, function (data, status) {
                // alert(data);
                location.reload();
            })
        });

        $('#buttonThreshold').click(function () {
            var url = base_url + 'user/update_threshold'
            $.post(url, {
                user_id: user_id,
                threshold: $('#threshold').val()
            }, function (data, status) {
                location.reload();
            })
        });

        // stripe
        $('#buttonSaveStripe').click(function () {
            var customer_id = $("#customer_id").val()
            const datausage_reset_at = $('#datausage_reset_at').val()
            const auto_pay = $('#checkAutoPay').is(':checked')
            var url = base_url + 'user/save_stripe'
            $.post(url, {
                user_id: user_id,
                customer_id: customer_id,
                datausage_reset_at: datausage_reset_at + '-03',
                auto_pay: auto_pay ? 1 : 0,
            }, function (data, status) {
                alert(data);
            })
            return false;
        });

        $('#buttonManuallJapierPay').click(function () {

            const auto_pay = $('#checkAutoPay').is(':checked')
            if (auto_pay == 1) {
                alert("Please unselect the Auto Pay option if you want to create the manual invoice");
            } else {
                var url = base_url + 'user/zapier_pay'
                $.post(url, {
                    user_id: user_id,
                }, function (data, status) {
                    alert(data);
                })
                return false;
            }
        });

        $('#buttonLabelCameras').click(function () {
            var url = base_url + 'user/label_cameras'
            $.post(url, {
                user_id: user_id,
            }, function (data, status) {
                alert(data);
            })
            return false;
        });

        var org_id = "<?php echo $org_id; ?>"

        $('#modal-camera-assign').on('shown.bs.modal', function(e) {
            $.getJSON(base_url + 'camera/smart_cams', {
                org_id: org_id,
            }, function (json, status) {
                $('#camera_list').empty();
                for (i = 0; i < json.length; ++i) {
                    $('#camera_list').append('<option value="' + json[i].id + '">' + json[i].name + " (" + json[i].IMEI + ')</option>');
                }
            })
        });

        $('#button-assign-camera').click(function () {
            var camera_id = $("#camera_list").val()
            var url = base_url + 'camera/assign_camera'
            $.post(url, {
                user_id: user_id,
                org_id: org_id,
                camera_id: camera_id
            }, function (data, status) {
                var obj = JSON.parse(data);
                if (obj.result == 'ok') {
                    $('#modal-camera-assign').modal('hide');
                    tableCamera.ajax.reload(null, false);
                } else {
                    alert(obj.message);
                }
            })
            return false;
        });

        $(document).on('click', 'i.fa-remove', function () {
            const id = $(this).data('id')
            var x = confirm("Are you sure you want to delete?")
            var url = base_url + 'user/remove_camera'
            if (x) {
                $.post(url, {
                    user_id: user_id,
                    camera_id: id
                }, function (data, status) {
                    tableCamera.ajax.reload(null, false);
                })
            } else {
                return false
            }
        });

        $(document).on('click', 'i.fa-edit', function () {
            const linkurl = $(this).attr('data-link')
            location.href = linkurl
        });
    })
</script>