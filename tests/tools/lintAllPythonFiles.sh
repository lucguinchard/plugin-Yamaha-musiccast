#!/bin/sh

python3 -m pylint --rcfile=tests/.pylintrc --output-format=colorized tools.py
# Tests des sources
for file in `find . -name "*.py" ! -name "__init__.py"`;
do
    echo "Check $file with pylint"
    python3 -m pylint --rcfile=tests/.pylintrc --output-format=colorized $file
done
