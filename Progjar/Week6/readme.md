# Tugas 2 - FTP Socket Programming On python

Nama : Wahyu Adam Anandika

NIM : 1203220046

## Code Server

```python
import os
import socket

def file_size(filename):
    return os.path.getsize(filename) / (1024 * 1024)

def run_server(host='localhost', port=12345):
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind((host, port))
    server_socket.listen(1)
    print(f"Server running on {host}:{port}")
    
    while True:
        conn, addr = server_socket.accept()
        print(f"Connected by {addr}")

        while True:
            data = conn.recv(1024).decode()
            if not data:
                break
            print(f"Received command: {data}")
            command = data.split()[0]
            if command == 'ls':
                files = os.listdir('.')
                response = '\n'.join(files)
            elif command == 'rm':
                filename = data.split()[1]
                try:
                    os.remove(filename)
                    response = f"File {filename} deleted."
                except FileNotFoundError:
                    response = "File not found."
            elif command.startswith('download'):
                _, filename = data.split()
                try:
                    if not os.path.isfile(filename):
                        conn.sendall(b'ERROR: File not found.')
                    else:
                        conn.sendall(b'OK')
                        with open(filename, 'rb') as f:
                            chunk = f.read(1024)
                            while chunk:
                                conn.sendall(chunk)
                                chunk = f.read(1024)
                        conn.sendall(b'ENDOFFILE')
                        print(f"File {filename} has been sent.")
                except Exception as e:
                    response = f"ERROR: {str(e)}"
                    conn.sendall(response.encode())
            elif command.startswith('upload'):
                _, filename, new_filename = data.split()
                response = 'OK'.encode()
                conn.sendall(response)
                with open(new_filename, 'wb') as f:
                    while True:
                        data = conn.recv(1024)
                        if not data:
                            break
                        f.write(data)
                print(f"File uploaded as {new_filename}.")
            elif command == 'size':
                filename = data.split()[1]
                try:
                    response = f"Size of {filename}: {file_size(filename):.2f} MB"
                except FileNotFoundError:
                    response = "File not found."
            elif command == 'byebye':
                conn.close()
                print("Connection closed.")
                break
            elif command == 'connme':
                response = "Connection established."
            else:
                response = "Invalid command."
            
            conn.sendall(response.encode())

        conn.close()

if __name__ == "__main__":
    run_server()
```


## Code Client

```python
import socket

def run_client(host='localhost', port=12345):
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    client_socket.connect((host, port))
    
    while True:
        command = input("Enter command: ")
        if command == 'byebye':
            client_socket.sendall(command.encode())
            break
        elif command.startswith('download'):
            _, filename, new_filename = command.split()
            client_socket.sendall(f'download {filename}'.encode())
            response = client_socket.recv(1024).decode()
            if response == 'OK':
                with open(new_filename, 'wb') as f:
                    while True:
                        data = client_socket.recv(1024)
                        if b'ENDOFFILE' in data:
                            f.write(data.replace(b'ENDOFFILE', b''))
                            break
                        f.write(data)
                print(f"File {filename} downloaded as {new_filename}.")
            else:
                print(response)
        elif command.startswith('upload'):
            _, filename, new_filename = command.split()
            client_socket.sendall(f'upload {filename} {new_filename}'.encode())
            response = client_socket.recv(1024).decode()
            if response == 'OK':
                with open(filename, 'rb') as f:
                    chunk = f.read(1024)
                    while chunk:
                        client_socket.sendall(chunk)
                        chunk = f.read(1024)
                print(f"File {filename} uploaded as {new_filename}.")
            else:
                print(response)
        else:
            client_socket.sendall(command.encode())
            response = client_socket.recv(1024).decode()
            print("Response:", response)
    
    client_socket.close()

if __name__ == "__main__":
    run_client()

```


## Deskripsi


Program diatas adalah sebuah sistem File Transfer Protocol (FTP) sederhana yang dibuat dengan bahasa Python menggunakan soket TCP/IP. Program ini terdiri dari dua bagian utama: **server dan client**. Server bertugas menerima perintah dari client, melakukan operasi yang diminta, dan mengirimkan balasan atau data kembali ke client. Client bertugas mengirimkan perintah ke server dan menampilkan respons dari server.

### 1. Server

Server berjalan pada host dan port (`localhost` dan `12345`) dan terus menerima koneksi dari client. Setelah terhubung, server dapat menerima beberapa perintah dari client dan melakukan aksi sesuai dengan perintah tersebut:

- `ls`: Menampilkan daftar file dan folder pada direktori saat ini di server.
- `rm {nama file}`: Menghapus file yang ditentukan dari direktori saat ini di server.
- `download {nama file asal}`: Mengirimkan file yang ditentukan dari server ke client.
- `upload {nama file asal} {nama file tujuan}`: Menerima file dari client dan menyimpannya di server dengan nama baru.
- `size {nama file}`: Menampilkan ukuran file yang ditentukan di server dalam satuan MB.
- `byebye`: Memutus koneksi client dari server.
- `connme`: Mengonfirmasi bahwa koneksi telah terestabilisasi (terutama berguna untuk inisiasi awal koneksi).

### 2. Client

Client terhubung ke server melalui host dan port yang ditentukan. Setelah terhubung, pengguna dapat memasukkan perintah-perintah yang akan dikirim ke server. Berikut adalah cara menggunakan setiap perintah yang tersedia:

- **ls**: Untuk melihat daftar file dan direktori pada server, cukup ketik `ls` lalu tekan Enter. Contoh penggunaan dan output :
```
Enter command: ls
Response: coba1.txt
coba2.txt
coba3.txt
coba4.txt
coba5.txt
Interaksi Manusia dan Komputer    
Kecerdasan Buatan
PEMODELAN DAN SIMULASI
Pemrograman Jaringan
Pemrograman Web
PERANCANGAN DAN ANALISIS ALGORITMA
Template Laporan.docx
Enter command: 
```

- **rm**: Untuk menghapus file pada server, ketik `rm {nama file}`, gantikan `{nama file}` dengan nama file yang ingin dihapus, lalu tekan Enter. Contoh penggunaan dan output :
```
Enter command: rm coba5.txt
Response: File coba5.txt deleted.
Enter command:
```
  
- **download**: Untuk mendownload file dari server, ketik `download {nama file asal} {nama file tujuan}`. `{nama file asal}` adalah nama file di server yang ingin di-download, dan `{nama file tujuan}` adalah nama file yang akan disimpan di client. Contoh penggunaan dan output :
```
Enter command: download coba1.txt coba5.txt
File coba1.txt downloaded as coba5.txt.
Enter command:
```
  
- **upload**: Untuk mengupload file ke server, ketik `upload {nama file asal} {nama file tujuan}`. `{nama file asal}` adalah nama file di client yang ingin di-upload, dan `{nama file tujuan}` adalah nama file yang akan disimpan di server. Contoh penggunaan dan output :
```
Enter command: upload coba1.txt coba6.txt
File coba1.txt uploaded as coba6.txt.
Enter command:
```
  
- **size**: Untuk mengetahui ukuran file di server, ketik `size {nama file}`, gantikan `{nama file}` dengan nama file yang ukurannya ingin diketahui, lalu tekan Enter. Contoh penggunaan dan output :
```
Enter command: size coba4.txt
Response: Size of coba4.txt: 0.44 MB
Enter command:
```
  
- **byebye**: Untuk memutus koneksi dengan server, ketik `byebye` lalu tekan Enter. Contoh penggunaan dan output :
```
Enter command: byebye
PS E:\Kuliah\Semester 4> 
```

- **connme**: Biasanya tidak digunakan oleh client secara manual, tetapi bisa digunakan untuk menguji koneksi dengan server dengan mengetik `connme` lalu tekan Enter. Contoh penggunaan dan output :
```
Enter command: connme
Response: Connection established.
Enter command: 
```