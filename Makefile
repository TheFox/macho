
CFLAGS = -Wall -O3

.PHONY: all
all:

tmp/test_data/test_prog: tests/test_prog.c
	$(CC) $< -o $@

examples/example1: examples/example1.o
	$(CC) $(CFLAGS) $< -o $@

examples/example1.o: examples/example1.c
	$(CC) $(CFLAGS) -c $< -o $@

examples/example2: examples/example2.o
	$(CC) $(CFLAGS) $< -o $@

examples/example2.o: examples/example1.c
	$(CC) $(CFLAGS) -c $< -o $@
