#!/usr/bin/env bash

function process()
{
    for (( pno=1; pno<=1; pno++ )); do
        # echo "i=${i} pno=${pno}";
        php index.php pno=${pno} &
    done;
}
process;
# for (( i=0; i<60; i++ )); do process; sleep 1; done;