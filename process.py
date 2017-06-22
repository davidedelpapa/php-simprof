#!/usr/bin/env python
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
'''
process.py
Processes the simple profiler's files to output the clean code
Ver 0.1 - (c) 2017, Davide Del Papa, Public Domain
'''
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
import argparse
import sys

__version__ = '0.1'
profiler_keywords = [
    'sp_flag', 
    'sp_prepare_report',
    'sp_print_report'
    ]

def process_lines(args):
    '''
    with open(args.infile) as input_file, open(args.outfile, 'w') as output_file:
        for line in input_file:
            if not any(profiler_keyword in line for profiler_keyword in profiler_keywords):
                output_file.write(line)
    '''
    for line in args.infile:
        if not any(profiler_keyword in line for profiler_keyword in profiler_keywords):
           args.outfile.write(line)

# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
# COMMAND LINE PARSER
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

parser = argparse.ArgumentParser(
    prog='process',
    description="Processes the simple profiler's files to output clean code",
    epilog='%(prog)s 0.1 - (c) 2017, Davide Del Papa, Public Domain'
)

parser.add_argument('-v', '--version', action='version', version="%(prog)s " + __version__, help = "Program's version")

parser.add_argument('infile', nargs='1', type=argparse.FileType('r'), default=sys.stdin)
parser.add_argument('outfile', nargs='?', type=argparse.FileType('w'), default=sys.stdout)

parser.set_defaults(func=process_lines)
args = parser.parse_args()
args.func(args)
