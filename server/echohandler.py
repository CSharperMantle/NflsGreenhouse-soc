from socketserver import TCPServer, StreamRequestHandler

class EchoHandler(StreamRequestHandler):
    def handle(self):
        print("Got connection from ", self.client_address)
        for line in self.rfile:
            self.wfile.write(line)


if __name__ == '__main__':
    server = TCPServer(('', 20000), EchoHandler)
    print('Serving on ', server.server_address)
    server.serve_forever()
