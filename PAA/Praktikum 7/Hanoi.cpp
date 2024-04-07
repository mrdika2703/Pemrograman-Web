#include <iostream>
using namespace std;

// Fungsi rekursif untuk menyelesaikan Tower of Hanoi
void towerOfHanoi(int n, string dari, string ke, string menggunakan, int ronde) {
    // Kasus dasar: jika hanya ada 1 gelang, pindahkan langsung ke tujuan
    if (n == 1) {
        cout << "Pindahkan gelang 1 dari " << dari << " ke " << ke << endl;
        return;
    }
    // Langkah rekursif: pindahkan n-1 gelang ke tiang transit
    towerOfHanoi(n-1, dari, menggunakan, ke, ronde);
    // Pindahkan gelang terakhir (terbesar) ke tujuan
    cout << "Pindahkan gelang " << n << " dari " << dari << " ke " << ke << endl;
    // Pindahkan n-1 gelang dari tiang transit ke tiang tujuan
    towerOfHanoi(n-1, menggunakan, ke, dari, ronde);
}

int main() {
    int T, n;
    // Minta pengguna untuk memasukkan jumlah ronde
    cout << "Masukkan jumlah ronde: ";
    cin >> T;

    for (int i = 1; i <= T; i++) {
        // Untuk setiap ronde, minta pengguna memasukkan jumlah gelang
        cout << "Masukkan jumlah gelang untuk Ronde " << i << ": ";
        cin >> n;
        // Tampilkan ronde dan mulai proses pemindahan gelang
        cout << "Ronde " << i << ":" << endl;
        cout << "Langkah-langkah epik Anto dalam memindahkan gelang:" << endl;
        towerOfHanoi(n, "Base of Power", "Tower of Triumph", "Booster Platform", i);
        cout << endl;
    }

    return 0;
}