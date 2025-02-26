<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\EventFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * แสดงปฏิทินตารางงาน
     */
    public function index()
    {
        return view('schedules.calendar');
    }

    /**
     * ดึงข้อมูลตารางงานเป็น JSON
     */
    public function show(Schedule $schedule)
    {
        return response()->json($schedule->load('files'));
    }

    /**
     * ดึง event ทั้งหมดสำหรับ FullCalendar
     */
    public function getEvents()
    {
        $now = Carbon::now('Asia/Bangkok');
        $schedules = Schedule::with('files')->get();

        $events = $schedules->map(function ($schedule) use ($now) {
            $endTime = Carbon::parse($schedule->end)->setTimezone('Asia/Bangkok');

            if ($endTime->lt($now) && $schedule->status !== 'เสร็จสิ้น') {
                $schedule->update(['status' => 'เสร็จสิ้น']);
            }

            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => Carbon::parse($schedule->start)->setTimezone('Asia/Bangkok')->toIso8601String(),
                'end' => $endTime->toIso8601String(),
                'backgroundColor' => $schedule->color,
                'borderColor' => $schedule->color,
                'extendedProps' => [
                    'description' => $schedule->description,
                    'status' => $schedule->status,
                    'color' => $schedule->color,
                    'files' => $schedule->files->map(fn($file) => [
                        'id' => $file->id,
                        'file_path' => $file->file_path
                    ])
                ],
                'color' => $schedule->status === 'เสร็จสิ้น' ? '#dc3545' : '#007bff',
            ];
        });

        return response()->json($events);
    }

    /**
     * จัดรูปแบบข้อมูล Event ให้เหมาะกับ FullCalendar
     */
    private function formatEvent($schedule, $now)
    {
        $endTime = Carbon::parse($schedule->end)->setTimezone('Asia/Bangkok');

        if ($endTime->lt($now) && $schedule->status !== 'เสร็จสิ้น') {
            $schedule->update(['status' => 'เสร็จสิ้น']);
        }

        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'start' => Carbon::parse($schedule->start)->setTimezone('Asia/Bangkok')->toIso8601String(),
            'end' => $endTime->toIso8601String(),
            'backgroundColor' => $schedule->color,
            'borderColor' => $schedule->color,
            'extendedProps' => [
                'description' => $schedule->description,
                'status' => $schedule->status,
                'color' => $schedule->color,
                'files' => $schedule->files->map(fn($file) => [
                    'id' => $file->id,
                    'file_path' => $file->file_path
                ]),
            ],
            'color' => $schedule->status === 'เสร็จสิ้น' ? '#dc3545' : '#007bff',
        ];
    }

    /**
     * บันทึกตารางงานใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:100',
            'color' => 'required|string|max:7',
            'files.*' => 'file|max:25600'
        ]);

        $validated['start'] = Carbon::parse($validated['start'])->toDateTimeString();
        $validated['end'] = Carbon::parse($validated['end'])->toDateTimeString();
        $validated['status'] = $validated['status'] ?? 'กำลังดำเนินการ';

        $schedule = Schedule::create($validated);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads', 'public');
                EventFile::create([
                    'schedule_id' => $schedule->id,
                    'file_path' => $path
                ]);
            }
        }

        return response()->json(['message' => 'Event created successfully', 'schedule' => $schedule], 201);
    }

    /**
     * อัปเดตตารางงาน
     */
    public function update(Request $request, Schedule $schedule)
    {
        // ตรวจสอบข้อมูล
        $validated = $this->validateSchedule($request);

        // แปลงวันที่ให้เป็น DateTime String
        $validated['start'] = Carbon::parse($validated['start'])->toDateTimeString();
        $validated['end'] = Carbon::parse($validated['end'])->toDateTimeString();

        if (!isset($validated['status'])) {
            $validated['status'] = $schedule->status;
        }
        // อัปเดตข้อมูลตารางงาน
        $schedule->update($validated);

        // ✅ ลบไฟล์ที่ถูกทำเครื่องหมายว่าต้องการลบ
    if ($request->has('deletedFiles')) {
        foreach ($request->deletedFiles as $fileId) {
            $file = EventFile::find($fileId);
            if ($file) {
                Storage::disk('public')->delete($file->file_path); // ลบไฟล์จาก storage
                $file->delete(); // ลบข้อมูลไฟล์จาก database
            }
        }
    }

    // ✅ อัปโหลดไฟล์ใหม่
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads', 'public');
            EventFile::create([
                'schedule_id' => $schedule->id,
                'file_path' => $path
            ]);
        }
    }

    return response()->json(['message' => 'Updated successfully', 'schedule' => $schedule]);
    }


    /**
     * ลบตารางงาน
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    /**
     * อัปเดตสถานะของตารางงาน
     */
    public function updateStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    /**
     * ลบไฟล์ที่แนบมา
     */
    public function deleteFile($id)
    {
        // ค้นหาไฟล์จากฐานข้อมูล
        $file = EventFile::find($id);

        if (!$file) {
            return response()->json(['message' => 'ไม่พบไฟล์ที่ต้องการลบ'], 404);
        }

        // ลบไฟล์จากที่เก็บใน storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // ลบข้อมูลไฟล์จากฐานข้อมูล
        $file->delete();

        return response()->json(['message' => 'ลบไฟล์เรียบร้อย']);
    }
    /**
     * ตรวจสอบข้อมูลที่ส่งมา
     */
    private function validateSchedule($request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:100',
            'color' => 'required|string|max:7',
            'files.*' => 'file|max:25600'
        ]);
    }

    /**
     * จัดการไฟล์แนบ
     */
    private function handleFileUploads($request, $scheduleId)
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads', 'public');
                EventFile::create([
                    'schedule_id' => $scheduleId,
                    'file_path' => $path
                ]);
            }
        }
    }
}