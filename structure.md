# Structure plan

Bikin _class_ **kalenderIKM**.

Functionalities :

 1. Show. Show what events are deployed already.
 2. Add. Add events to certain date, in certain times. **If valid.**

## Events

Setiap _event_ punya properti ini.

 1. Nama
 2. Waktu
 3. Penyelenggara
 4. Tipe / target peserta _(tie breaker!)_

## Tipe Event

 1. Umum.
 2. IKM (seluruhnya / lebih dari 1 angkatan).
 3. Internal Himpunan / Badan.
 4. Lainnya ? (contoh: PPAA, kunpas)

**Rules**
 1. (1) dan (2) tidak boleh bertabrakan dengan event apapun.
 2. (3) boleh bertabrakan dengan sesama (3)
