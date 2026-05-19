import os
import re

directory = 'c:/xampp/htdocs/sistem-pkl-v13/resources/views'

for root, dirs, files in os.walk(directory):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
                
            # Replace text-slate-900 dark:text-white with text-white if bg-blue-600 is present on the same line
            # Wait, regular expressions are better.
            
            lines = content.split('\n')
            changed = False
            for i, line in enumerate(lines):
                if 'bg-blue-600' in line and 'text-slate-900 dark:text-white' in line:
                    lines[i] = line.replace('text-slate-900 dark:text-white', 'text-white')
                    changed = True
                elif 'bg-indigo-600' in line and 'text-slate-900 dark:text-white' in line:
                    lines[i] = line.replace('text-slate-900 dark:text-white', 'text-white')
                    changed = True
                    
            if changed:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write('\n'.join(lines))
                print(f"Updated {filepath}")
