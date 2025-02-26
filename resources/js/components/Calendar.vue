

<template>
  <FullCalendar :options="calendarOptions" />
</template>

<script>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import axios from "axios";

export default {
  components: { FullCalendar },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin],
        initialView: "dayGridMonth",
        events: [],
      },
    };
  },
  async mounted() {
    let { data } = await axios.get("/api/schedules");
    this.calendarOptions.events = data.map(event => ({
      title: event.title,
      start: event.start_time,
      end: event.end_time,
    }));
  },
};
</script>
