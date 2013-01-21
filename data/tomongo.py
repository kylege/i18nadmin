#! /usr/bin/python
#!encoding=utf8

from optparse import OptionParser
import xml.etree.ElementTree as ET
import pymongo

parser = OptionParser()
parser.add_option("-l", "--lang", dest="langname",
                  help="语言名称，如cs")
parser.add_option("-f", "--file", dest='langfile',
                  help="语言的xml文件")

(options, args) = parser.parse_args()

LANG = options.langname
FILE = options.langfile

root = ET.parse(FILE).getroot()
#print root.childNodes
count = 1
conn = pymongo.Connection('10.8.8.11')
#conn = pymongo.Connection()
DBNAME = 'lamin'
TABLE_ITEMS = 'la_items'
db = conn[DBNAME]
col = db[TABLE_ITEMS]
for node in root:
    record = {'la_id':count, 'key':node.tag, LANG:node.text, 'tags':[]}
    res = col.find_one({'key': node.tag})
    if res:
        col.update({'key':node.tag}, {'$set':{LANG: node.text}})
    else:
        col.insert(record)
        count = count + 1
    record = {'la_id':count, 'key':node.tag, LANG:node.text, 'tags':[]}
    res = col.find_one({'key': node.tag})
    if res:
        col.update({'key':node.tag}, {'$set':{LANG: node.text}})
    else:
        col.insert(record)
        count = count + 1
