from socketserver import TCPServer, StreamRequestHandler
from datetime import datetime
from pprint import pprint

accepted = [0xF1, 0x01, 0x00, 0x00, 0x00, 0xFF, 0x00, 0XF2]

class EchoHandler(StreamRequestHandler):
    def handle(self):
        print(datetime.today(), 'Got connection from ', self.client_address)
        content = []
        for line in self.rfile:
            content.append(line)
            # self.wfile.write(line)
            self.wfile.write(accepted)
        pprint(content)
        

if __name__ == '__main__':
    server = TCPServer(('', 20000), EchoHandler)
    print(datetime.today(), 'Serving on ', server.server_address)
    server.serve_forever()
