import socket
server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
HOST = 'localhost'
PORT = 12345
server_socket.bind((HOST, PORT))
server_socket.listen(1)
print("Waiting...")
client_socket, client_address = server_socket.accept()
data = client_socket.recv(1024)
pesan = data.decode()
print("Request dari client :", pesan, "IP client :", client_address)
jumlah_karakter = len(pesan)
response = f"Jumlah karakter: {jumlah_karakter}"
client_socket.sendall(response.encode())
client_socket.close()
server_socket.close()
