@extends('layouts.app')

@section('content')
<div class="container">
    <h2>ปฏิทินตารางงาน2</h2>
    <div id="calendar"></div>
</div>

<!-- Modal สำหรับเพิ่ม/แก้ไขตารางงาน -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่ม/แก้ไขตารางงาน</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    @csrf
                    <input type="hidden" id="event_id">
                    <div class="form-group">
                        <label>ชื่อกิจกรรม</label>
                        <input type="text" id="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>รายละเอียด</label>
                        <textarea id="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>วันเริ่ม</label>
                        <input type="datetime-local" id="start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>วันสิ้นสุด</label>
                        <input type="datetime-local" id="end" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>สถานะ</label>
                        <select id="status" class="form-control">
                            <option value="pending">รอดำเนินการ</option>
                            <option value="confirmed">ยืนยัน</option>
                            <option value="completed">เสร็จสิ้น</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    <button type="button" id="deleteEvent" class="btn btn-danger">ลบ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar & jQuery -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
    console.log("FullCalendar โหลดแล้ว...");

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        editable: true,
        selectable: true,
        events: "{{ route('schedules.events') }}",
        dateClick: function (info) {
            $('#eventModal').modal('show');
            $('#event_id').val('');
            $('#title').val('');
            $('#description').val('');
            $('#start').val(info.dateStr);
            $('#end').val(info.dateStr);
            $('#status').val('pending');
        },
        eventClick: function (info) {
            $('#eventModal').modal('show');
            $('#event_id').val(info.event.id);
            $('#title').val(info.event.title);
            $('#description').val(info.event.extendedProps.description);
            $('#start').val(info.event.start.toISOString().slice(0, 16));
            $('#end').val(info.event.end ? info.event.end.toISOString().slice(0, 16) : '');
            $('#status').val(info.event.extendedProps.status);
        }
    });

    calendar.render();

    $('#eventForm').on('submit', function (e) {
        e.preventDefault();
        let id = $('#event_id').val();
        let url = id ? "/schedules/update/" + id : "/schedules/store";
        $.post(url, {
            _token: "{{ csrf_token() }}",
            title: $('#title').val(),
            description: $('#description').val(),
            start: $('#start').val(),
            end: $('#end').val(),
            status: $('#status').val()
        }, function () {
            $('#eventModal').modal('hide');
            calendar.refetchEvents();
        });
    });

    $('#deleteEvent').click(function () {
        let id = $('#event_id').val();
        if (id) {
            $.post("/schedules/delete/" + id, { _token: "{{ csrf_token() }}" }, function () {
                $('#eventModal').modal('hide');
                calendar.refetchEvents();
            });
        }
    });
</script>

@endsection
