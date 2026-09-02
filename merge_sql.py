import os

schema_file = r'd:\www\finaflow\schema_dump.sql'
data_file = r'c:\Users\Administrator\Downloads\if0_40454563_finaflow_data_only.sql'
output_file = r'c:\Users\Administrator\Downloads\if0_40454563_finaflow_FULL_FIXED.sql'

with open(schema_file, 'r', encoding='utf-8') as f:
    schema_content = f.read()

with open(data_file, 'r', encoding='utf-8') as f:
    data_content = f.read()

final_content = """
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = '+00:00';
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
""" + "\n\n-- SCHEMA STRUCTURE --\n\n" + schema_content + "\n\n-- DATA INSERTS --\n\n" + data_content + "\n\nCOMMIT;\n"

with open(output_file, 'w', encoding='utf-8') as f:
    f.write(final_content)

print('Full SQL merged successfully into if0_40454563_finaflow_FULL_FIXED.sql!')
