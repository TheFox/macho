
/*
Compiled on Mac OS X 10.10 with

:> cc -v
Apple LLVM version 6.0 (clang-600.0.56) (based on LLVM 3.5svn)
Target: x86_64-apple-darwin14.0.0
Thread model: posix

This binary should look like

		0x100000f40 <example1`main>: 55                    pushq  %rbp
		0x100000f41 <example1`main+1>: 48 89 e5              movq   %rsp, %rbp
		0x100000f44 <example1`main+4>: 48 8d 3d 37 00 00 00  leaq   0x37(%rip), %rdi          ; "hello world"
		0x100000f4b <example1`main+11>: e8 10 00 00 00        callq  0x100000f60               ; symbol stub for: puts
		0x100000f50 <example1`main+16>: 48 8d 3d 37 00 00 00  leaq   0x37(%rip), %rdi          ; "a second line"
		0x100000f57 <example1`main+23>: e8 04 00 00 00        callq  0x100000f60               ; symbol stub for: puts
		0x100000f5c <example1`main+28>: 31 c0                 xorl   %eax, %eax
		0x100000f5e <example1`main+30>: 5d                    popq   %rbp
		0x100000f5f <example1`main+31>: c3                    retq

Otherwise you must change the offset in the PHP script.
*/

#include <stdio.h>

int main(int argc, char const *argv[]){
	puts("hello world");
	puts("a second line");
	return 0;
}
