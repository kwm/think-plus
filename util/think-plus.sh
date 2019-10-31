#!/usr/bin/env bash

vendorPath=$(readlink -f "$(dirname "$0")/../")

patch -p1 -d $vendorPath/topthink/framework < $vendorPath/kwm/think-plus/patch/think-plus.patch