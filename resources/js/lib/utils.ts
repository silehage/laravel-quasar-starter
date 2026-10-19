import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export function can(ability: string) {
    if (ability == 'All') {
        return true;
    }

    const user = computed(() => usePage().props.auth.user)
    const permissions = computed(() => usePage().props.auth.permissions)

    if (permissions.value.includes('can-all')) {
        return true;
    }

    if (!user.value || !permissions.value.length) {
        return false
    }

    if (!ability) {
        return false
    }

    return permissions.value.includes(ability)

}

export function badgeColor(status) {
    let color = 'grey-7'
    switch (status.toLowerCase()) {
        case 'processed':
            color = 'amber-9'
            break;
        case 'shipped':
            color = 'teal'
            break;
        case 'completed':
            color = 'green'
            break;
    }
    return color
}

export function dateFormat(date) {
    if (!date) return ''
    const d = new Date(date);

    let options = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        // second: 'numeric',
        // timeZoneName: 'short',
    };

    return new Intl.DateTimeFormat('id', options).format(d);

}

export function numberFormat(num: number | string) {
    if (!num) return '0'
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

export function cleaningString(val: string) {
    return val.replace(/\D/g, '')
}

export function getDiffInDays(datestring: string) {
    const date1 = new Date();
    const date2 = new Date(datestring);

    // One day in milliseconds
    const oneDay = 1000 * 60 * 60 * 24;

    // Calculating the time difference between two dates
    const diffInTime = date2.getTime() - date1.getTime();

    // Calculating the no. of days between two dates
    const diffInDays = Math.round(diffInTime / oneDay);

    return diffInDays;
}

export function formatQueryparams(url: string, query: Object) {

    console.log(query);

    let q = {};

    for (const x in query) {

        console.log(query[x]);

        // if (query[x] != undefined) {

        //     q[x] = query[x]
        // }
    }

    if (Object.entries(q).length) {
        url = `${url}?${new URLSearchParams(q)}`;
    }
    return url
}

export function getDiffDate(dateString, endDateString = null ,reverse = false) {
    
    // 1. Ubah string tanggal menjadi objek Date
    const deadline = new Date(dateString);
    let sekarang = new Date();
    
    if(endDateString) {
        sekarang = new Date(endDateString)
    }

    // 2. Set waktu ke jam 00:00:00 agar perbandingan hari lebih akurat (mengabaikan jam/menit)
    deadline.setHours(0, 0, 0, 0);
    sekarang.setHours(0, 0, 0, 0);

    let selisihWaktu = deadline - sekarang;

    // 3. Hitung selisih waktu dalam milidetik
    if(reverse == true) {
         selisihWaktu = sekarang - deadline;
    }

    // 4. Konversi milidetik ke hari (1 hari = 24 jam * 60 menit * 60 detik * 1000 ms)
    const selisihHari = selisihWaktu / (1000 * 60 * 60 * 24);

    return selisihHari;

}
export function getNotifColor(dateString) {

    const selisihHari = getDiffDate(dateString)

    // 5. Logika penentuan warna
    if (selisihHari < 0) {
        return 'red'; // Sudah lewat / terlambat
    } else if (selisihHari <= 2) {
        return 'orange'; // Kurang dari atau sama dengan 1 hari (kritis)
    } else {
        return 'green'; // Masih aman
    }
}

export function getNotifClass(dateString) {

    const selisihHari = getDiffDate(dateString)

    // 5. Logika penentuan warna
    if (selisihHari < 0) {
        return 'bg-red'; // Sudah lewat / terlambat
    } else if (selisihHari <= 2) {
        return 'bg-orange'; // Kurang dari atau sama dengan 1 hari (kritis)
    } else {
        return 'bg-green'; // Masih aman
    }
}


