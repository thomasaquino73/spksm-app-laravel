let publishDatePicker;
const publishDates = document.querySelector("#publishDate");

if (publishDates) {
    publishDatePicker = flatpickr(publishDates, {
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        minDate: "today",
        defaultDate: new Date(), // ⬅️ otomatis set tanggal & jam sekarang
        time_24hr: true, // ⬅️ pakai format 24 jam (opsional)
    });
}

function toggleDatePicker() {
    const publishLater = document.getElementById("publishLater").checked;
    const datePicker = document.getElementById("datePickerContainer");

    if (publishLater) {
        datePicker.style.display = "block";

        // ⬅️ Saat tombol "Publish Later" diklik, isi ulang waktu sekarang
        if (publishDatePicker) {
            publishDatePicker.setDate(new Date(), true);
        }
    } else {
        datePicker.style.display = "none";
        // Opsional: kosongkan jika ingin reset nilai
        // document.getElementById('publishDate').value = '';
    }
}
