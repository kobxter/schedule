@extends('layouts.app')

@section('content')
<style>
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
        font-size: 1rem; /* ปรับขนาดตัวอักษร */
    }
    .modal-header {
        padding: 1rem 1.5rem;
        background-color: #007bff; /* สี header */
        color: #fff; /* สีตัวอักษร */
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-body p {
        margin-bottom: 1rem;
        font-size: 0.95rem;
        line-height: 1.5; /* เพิ่มความอ่านง่าย */
    }
    .modal-footer {
        padding: 1rem 1.5rem;
    }
    .modal-footer .btn {
        font-size: 0.9rem;
    }
    /* ปรับสีและลักษณะของ FullCalendar */
    #calendar {
        margin: auto;
        color: #333;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 15px;
    }
    .fc-toolbar {
        background-color: #444;
        color: #fff;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }
    .fc-col-header-cell {
        background-color: #444;
        color: #fff;
        font-weight: bold;
        padding: 10px;
        text-align: center;
    }
    .fc-event {
        color: #fff;
        background-color: #007bff;
        border: none;
    }
    .fc-daygrid-day-number {
        color: #333;
    }
    .fc-button {
        background-color: #444;
        color: #fff;
        border: none;
    }
    .fc-button:hover {
        background-color: #666;
    }
    .banner {
        text-align: center;
        padding: 15px;
        background-color: #f8f9fa; /* สีพื้นหลังอ่อน */
        margin-bottom: 20px;
    }

    .banner-logo {
        width: 200px; /* ปรับขนาดโลโก้ */
        height: auto;
    }
    #calendar-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh; /* ให้ปฏิทินอยู่ตรงกลาง */
    }
    #calendar {
        max-width: 80%;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }
    #color-picker {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.color-option {
    display: flex;
    align-items: center;
    cursor: pointer;
}
.color-option input {
    display: none;
}
.color-box {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: inline-block;
    cursor: pointer;
    border: 2px solid transparent;
}
.color-option input:checked + .color-box {
    border: 2px solid #000;
}
</style>
<div id="calendar"></div>
</div>
<!-- Modal รายละเอียด/แก้ไข -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventDetailsModalLabel">
                    <i class="fas fa-calendar-alt"></i> รายละเอียด
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetailsContent">
                    <!-- ข้อมูลรายละเอียด/แบบฟอร์มแก้ไขจะแสดงที่นี่ -->
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ปิด
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal แจ้งเตือน -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="alertModalLabel">แจ้งเตือน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="alertMessage"></p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal ยืนยันการลบ -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>คุณแน่ใจหรือไม่ว่าต้องการลบกิจกรรมนี้?</p>
                <input type="hidden" id="deleteEventId">
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">ลบ</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal แสดงตัวอย่างไฟล์ -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewModalLabel">ดูตัวอย่างไฟล์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="previewContent">
                <!-- แสดงตัวอย่างไฟล์ที่นี่ -->
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<!-- FullCalendar & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

<!-- ตั้งค่า CSRF Token สำหรับ AJAX -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script>
// ฟังก์ชันสำหรับฟอร์แมตวันที่และเวลาเป็นแบบไทย
const formatDateTime = (date) => {
    return new Intl.DateTimeFormat('th-TH', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date) + ' น.';
};
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        timeZone: 'Asia/Bangkok',  // ตั้งค่า Timezone ให้ตรงกับไทย
        initialView: 'dayGridMonth',
        editable: false,
        events: '/schedules', // โหลดข้อมูลจาก Controller
        eventTimeFormat: { // กำหนดรูปแบบเวลาให้แสดงเต็ม
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false  // ปิด AM/PM เพื่อใช้รูปแบบ 24 ชั่วโมง
        },
        eventDidMount: function(info) {
        let event = info.event;
        // ถ้ามีค่า color จาก database ให้ใช้
        if (event.backgroundColor) {
            info.el.style.backgroundColor = event.backgroundColor;
            info.el.style.borderColor = event.backgroundColor;
        }
        let now = new Date();
        if (event.extendedProps.color) {  
                info.el.style.backgroundColor = event.extendedProps.color;  
            }
        if (event.end && event.end < now) { 
            // เปลี่ยนสีของจุด event-dot ให้เป็นสีแดง
            const dot = info.el.querySelector('.fc-daygrid-event-dot');
            if (dot) {
                dot.style.backgroundColor = '#dc3545';
                dot.style.borderColor = '#dc3545';
            }

            // เปลี่ยนสถานะเป็น "เสร็จสิ้น" และอัปเดตไปที่เซิร์ฟเวอร์
            if (event.extendedProps.status !== "เสร็จสิ้น") {
                updateEventStatus(event.id, "เสร็จสิ้น");
            }
        }
    },
        eventClick: function(info) {
        let event = info.event;
        // ลดเวลาไป 7 ชั่วโมง
        let adjustedStart = new Date(event.start);
        adjustedStart.setHours(adjustedStart.getHours() - 7);
        let adjustedEnd = event.end ? new Date(event.end) : null;
        if (adjustedEnd) {
            adjustedEnd.setHours(adjustedEnd.getHours() - 7);
        }
        // สร้างรายการไฟล์ที่แนบไว้
    let fileListHTML = '';
    if (event.extendedProps.files && event.extendedProps.files.length > 0) {
        fileListHTML = '<p><strong>ไฟล์แนบ:</strong></p><ul>';
        event.extendedProps.files.forEach(file => {
            let filePath = `/storage/${file.file_path}`;
            let fileName = file.file_path.split('/').pop();
            let fileExt = fileName.split('.').pop().toLowerCase();
            let previewBtn = '';

            // ตรวจสอบประเภทไฟล์ (รองรับ JPG, PNG, PDF)
            if (['jpg', 'jpeg', 'png', 'pdf'].includes(fileExt)) {
                if (fileExt === 'pdf') {
                    previewBtn = `<button onclick="previewFile('${filePath}', 'pdf')" class="btn btn-sm btn-info">🔍 ดูไฟล์</button>`;
                } else {
                    previewBtn = `<button onclick="previewFile('${filePath}', 'image')" class="btn btn-sm btn-info">🖼️ ดูรูป</button>`;
                }
            }

            fileListHTML += `
                <li>
                    <a href="${filePath}" download>${fileName}</a>
                    ${previewBtn}
                </li>
            `;
        });
        fileListHTML += '</ul>';
    } else {
        fileListHTML = '<p><strong>ไฟล์แนบ:</strong> ไม่มีไฟล์แนบ</p>';
    }
        $('#eventDetailsContent').html(`
            <p><strong>หัวข้อ:</strong> ${event.title}</p>
            <p><strong>คำอธิบาย:</strong> ${event.extendedProps.description || ''}</p>
            <p><strong>สีของกิจกรรม:</strong> <input type="color" value="${event.extendedProps.color || '#007bff'}" disabled></p>
            <p><strong>วันเวลาเริ่มต้น:</strong> ${formatDateTime(adjustedStart)}</p>
            <p><strong>วันเวลาสิ้นสุด:</strong> ${adjustedEnd ? formatDateTime(adjustedEnd) : 'ไม่ระบุ'}</p>    
            ${fileListHTML} <!-- แสดงไฟล์แนบ -->   
            <p><strong>สถานะ:</strong> ${event.extendedProps.status || ''}</p>                
            <button class="btn btn-warning" onclick="loadEditForm(${event.id})">✏️ แก้ไข</button>
            <button class="btn btn-danger" onclick="deleteEvent(${event.id})">🗑️ ลบ</button>
        `);
        $('#eventDetailsModal').modal('show');
    },
        dateClick: function(info) {
            let selectedDate = info.dateStr + "T09:00"; // ตั้งค่าค่าเริ่มต้น
            $('#eventDetailsContent').html(`
                <p><strong>หัวข้อ:</strong>
                    <input type="text" id="title" class="form-control">
                </p>
                <p><strong>คำอธิบาย:</strong>
                    <textarea id="description" class="form-control" rows="3"></textarea>
                </p>
                <p><strong>เลือกสีของกิจกรรม:</strong></p>
                <div id="color-picker">
                    <label class="color-option"><input type="radio" name="color" value="#ff5733"> <span class="color-box" style="background:#ff5733;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#33ff57"> <span class="color-box" style="background:#33ff57;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#3357ff" checked> <span class="color-box" style="background:#3357ff;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#ff33a8"> <span class="color-box" style="background:#ff33a8;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#f3ff33"> <span class="color-box" style="background:#f3ff33;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#a833ff"> <span class="color-box" style="background:#a833ff;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#33fff6"> <span class="color-box" style="background:#33fff6;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#ff8c33"> <span class="color-box" style="background:#ff8c33;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#000000"> <span class="color-box" style="background:#000000;"></span></label>
                </div>
                <p><strong>แนบไฟล์:</strong></p>
                <input type="file" id="files" class="form-control" multiple>
                <p><strong>วันเวลาเริ่มต้น:</strong>
                    <input type="datetime-local" id="start" class="form-control" value="${selectedDate}">
                </p>
                <p><strong>วันเวลาสิ้นสุด:</strong>
                    <input type="datetime-local" id="end" class="form-control" value="${selectedDate.replace('09:00', '22:00')}">
                </p>
                <button class="btn btn-primary" onclick="saveEvent()">บันทึก</button>
            `);
            $('#eventDetailsModal').modal('show');
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week: 'สัปดาห์',
            day: 'วัน'
        },
        navLinks: true,
        dayMaxEvents: true,
    });
    calendar.render();
});
// ฟังก์ชันอัปเดตสถานะในฐานข้อมูล
function updateEventStatus(eventId, newStatus) {
    $.ajax({
        url: `/schedules/${eventId}/update-status`, // สมมติว่า API รองรับการอัปเดตเฉพาะสถานะ
        method: 'PUT',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: newStatus
        },
        success: function(response) {
            console.log(`Event ID ${eventId} ถูกอัปเดตเป็นสถานะ: ${newStatus}`);
        },
        error: function(response) {
            console.error('เกิดข้อผิดพลาดในการอัปเดตสถานะ', response);
        }
    });
}
// ฟังก์ชันบันทึกตารางงานใหม่
function saveEvent() {
    let formData = new FormData();
    formData.append("title", $('#title').val());
    formData.append("start", $('#start').val());
    formData.append("end", $('#end').val());
    formData.append("status", $('#status').val() || "กำลังดำเนินการ");
    formData.append("description", $('#description').val());
    formData.append("color", $('input[name="color"]:checked').val());

    // เพิ่ม CSRF Token ลงใน FormData
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    // เพิ่มไฟล์แนบเข้าไปใน FormData
    let files = $('#files')[0].files;
    for (let i = 0; i < files.length; i++) {
        formData.append("files[]", files[i]);
    }

    $.ajax({
        url: "/schedules",
        method: "POST",
        data: formData,
        processData: false, // ห้ามแปลงข้อมูล FormData
        contentType: false, // ห้ามตั้งค่า Content-Type เอง
        success: function (response) {
            showAlert("บันทึกข้อมูลสำเร็จ!", "success");
            setTimeout(() => location.reload(), 1000);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showAlert("เกิดข้อผิดพลาดในการบันทึกข้อมูล", "error");
        },
    });
}


// ฟังก์ชันโหลดฟอร์มแก้ไขตารางงาน
function loadEditForm(id) {
    $.ajax({
        url: `/schedules/${id}`, // ใช้ /schedules/{id} ตาม RESTful API
        method: 'GET',
        success: function(schedule) {
            deletedFiles = [];
            $('#eventDetailsContent').html(`
                <p><strong>หัวข้อ:</strong>
                    <input type="text" id="title" class="form-control" value="${schedule.title}">
                </p>
                <p><strong>คำอธิบาย:</strong>
                    <textarea id="description" class="form-control" rows="3">${schedule.description || ''}</textarea>
                </p>
                <p><strong>เลือกสีของกิจกรรม:</strong>
                <div id="color-picker">
                    <label class="color-option"><input type="radio" name="color" value="#ff5733" ${schedule.color === '#ff5733' ? 'checked' : ''}> <span class="color-box" style="background:#ff5733;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#33ff57" ${schedule.color === '#33ff57' ? 'checked' : ''}> <span class="color-box" style="background:#33ff57;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#3357ff" ${schedule.color === '#3357ff' ? 'checked' : ''}> <span class="color-box" style="background:#3357ff;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#ff33a8" ${schedule.color === '#ff33a8' ? 'checked' : ''}> <span class="color-box" style="background:#ff33a8;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#f3ff33" ${schedule.color === '#f3ff33' ? 'checked' : ''}> <span class="color-box" style="background:#f3ff33;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#a833ff" ${schedule.color === '#a833ff' ? 'checked' : ''}> <span class="color-box" style="background:#a833ff;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#33fff6" ${schedule.color === '#33fff6' ? 'checked' : ''}> <span class="color-box" style="background:#33fff6;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#ff8c33" ${schedule.color === '#ff8c33' ? 'checked' : ''}> <span class="color-box" style="background:#ff8c33;"></span></label>
                    <label class="color-option"><input type="radio" name="color" value="#000000" ${schedule.color === '#000000' ? 'checked' : ''}> <span class="color-box" style="background:#000000;"></span></label>
                </div></p>
                <p><strong>แนบไฟล์เพิ่มเติม:</strong>
                <input type="file" id="files" class="form-control" multiple>
                <div id="file-list">
                    ${schedule.files.map(file => `
                        <p id="file-${file.id}">
                            <a href="/storage/${file.file_path}" download>${file.file_path.split('/').pop()}</a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="markFileForDeletion(${file.id})">❌ ลบ</button>
                        </p>
                    `).join('')}
                </div></p>
                <p><strong>สถานะ:</strong>
                <select id="status" class="form-control">
                    <option value="กำลังดำเนินการ" ${schedule.status === 'กำลังดำเนินการ' ? 'selected' : ''}>กำลังดำเนินการ</option>
                    <option value="เสร็จสิ้น" ${schedule.status === 'เสร็จสิ้น' ? 'selected' : ''}>เสร็จสิ้น</option>
                    <option value="ยกเลิก" ${schedule.status === 'ยกเลิก' ? 'selected' : ''}>ยกเลิก</option>
                </select></p>
                <p><strong>วันเวลาเริ่มต้น:</strong>
                    <input type="datetime-local" id="start" class="form-control" value="${formatDateForInput(schedule.start)}">
                </p>
                <p><strong>วันเวลาสิ้นสุด:</strong>
                    <input type="datetime-local" id="end" class="form-control" value="${formatDateForInput(schedule.end)}">
                </p>
                <button class="btn btn-primary" onclick="updateEvent(${schedule.id})">💾 บันทึกการแก้ไข</button>
            `);
            $('#eventDetailsModal').modal('show');
        },
        error: function(response) {
            alert('เกิดข้อผิดพลาดในการโหลดฟอร์มแก้ไข');
        }
    });
}
function markFileForDeletion(fileId) {
    deletedFiles.push(fileId); // 👉 เพิ่ม ID ของไฟล์ที่ถูกลบเข้าไป
    $(`#file-${fileId}`).remove(); // 👉 ซ่อนไฟล์ที่ถูกลบจากฟอร์ม
}

// ฟังก์ชันแก้ไขตารางงาน
function updateEvent(id) {
    let formData = new FormData();
    formData.append('_method', 'PUT'); // Laravel ใช้ PUT method ผ่าน FormData
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('title', $('#title').val());
    formData.append('start', $('#start').val());
    formData.append('end', $('#end').val());
    formData.append('status', $('#status').val());
    formData.append('description', $('#description').val());
    formData.append('color', $('input[name="color"]:checked').val());
    // ✅ เพิ่มไฟล์ที่ต้องการลบลงใน formData
    deletedFiles.forEach(fileId => formData.append('deletedFiles[]', fileId));
    // เพิ่มไฟล์แนบ
    let files = $('#files')[0].files;
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    $.ajax({
        url: `/schedules/${id}`,
        method: 'POST',  // ใช้ POST + `_method=PUT` เพื่อรองรับ FormData
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            showAlert('แก้ไขข้อมูลสำเร็จ!', 'success');
            setTimeout(() => location.reload(), 1000);
        },
        error: function(response) {
            showAlert('เกิดข้อผิดพลาดในการแก้ไขข้อมูล', 'error');
        }
    });
}
// ฟังก์ชันลบตารางงาน
function deleteEvent(id) {
    $('#deleteEventId').val(id);  // เก็บค่า ID ของ Event ที่จะลบ
    $('#confirmDeleteModal').modal('show');  // เปิด Modal
}
// เมื่อกดปุ่ม "ลบ" ใน Modal
$('#confirmDeleteBtn').click(function () {
    let id = $('#deleteEventId').val();
    $.ajax({
        url: `/schedules/${id}/delete`,
        method: 'DELETE',
        success: function(response) {
            $('#confirmDeleteModal').modal('hide');  // ปิด Modal
            showAlert('ลบข้อมูลสำเร็จ!', 'success');
            setTimeout(() => location.reload(), 1000);
        },
        error: function(response) {
            showAlert('เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
        }
    });
});
function deleteFile(fileId) {
    if (!confirm('คุณต้องการลบไฟล์นี้ใช่หรือไม่?')) return;

    $.ajax({
        url: `/files/${fileId}`,
        method: 'DELETE',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            showAlert('ลบไฟล์สำเร็จ!', 'success');
            $(`#file-${fileId}`).remove();  // ลบ element ที่แสดงไฟล์
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            showAlert('เกิดข้อผิดพลาดในการลบไฟล์', 'error');
        }
    });
}

// ฟังก์ชันแปลงวันที่ให้อยู่ในรูปแบบ "YYYY-MM-DDTHH:mm" สำหรับ input type="datetime-local"
function formatDateForInput(dateStr) {
    let d = new Date(dateStr);
    let year = d.getFullYear();
    let month = ('0' + (d.getMonth() + 1)).slice(-2);
    let day = ('0' + d.getDate()).slice(-2);
    let hours = ('0' + d.getHours()).slice(-2);
    let minutes = ('0' + d.getMinutes()).slice(-2);
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}
// ฟังก์ชันเปิด Modal แจ้งเตือน
function showAlert(message, type = 'success') {
    $('#alertModalLabel').removeClass('bg-success bg-danger bg-warning');
    $('#alertModalLabel').addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
    $('#alertMessage').html(message);
    $('#alertModal').modal('show');
}
function previewFile(filePath, type) {
    let previewHTML = '';
    
    if (type === 'image') {
        previewHTML = `<img src="${filePath}" class="img-fluid" alt="Preview Image">`;
    } else if (type === 'pdf') {
        previewHTML = `<iframe src="${filePath}" width="100%" height="500px"></iframe>`;
    }

    $('#previewContent').html(previewHTML);
    $('#previewModal').modal('show');
}
</script>
@endsection