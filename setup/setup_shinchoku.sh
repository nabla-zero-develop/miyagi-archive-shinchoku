#!/bin/bash

chown -R apache:apache shinchoku
find shinchoku -type d | xargs chmod 755
find shinchoku -type f | xargs chmod 644
