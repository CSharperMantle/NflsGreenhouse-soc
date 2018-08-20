from socketserver import TCPServer, StreamRequestHandler
from datetime import datetime
from pprint import pprint

class EchoHandler(StreamRequestHandler):
    def handle(self):
        print(datetime.today(), "Got connection from ", self.client_address)
        content = []
        for line in self.rfile:
            content.append(line)
            self.wfile.write(line)
        pprint(content)
        

if __name__ == '__main__':
    server = TCPServer(('', 20000), EchoHandler)
    print('Serving on ', server.server_address)
    server.serve_forever()
