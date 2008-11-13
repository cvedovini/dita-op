#!/usr/bin/env python
# -*- coding: utf-8 -*-
##
#  Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.
#
#  This file is part of the DITA Open Platform <http://www.dita-op.org/>.
#
#  The DITA Open Platform is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  The DITA Open Platform is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with The DITA Open Platform.  If not, see <http://www.gnu.org/licenses/>.
##

import os
from setuptools import setup, find_packages

setup(
    name = 'DITA Open Platform',
    version = '1.0M2',
    description = 'Bitten commands for the DITA Open Toolkit',
    long_description = \
"""Bitten commands for the integration of the DITA Open Toolkit into Trac.""",
    author = 'dita-op.org',
    author_email = 'info@dita-op.org',
    license = 'GPL',
    url = 'http://www.dita-op.org/',
    download_url = 'http://www.dita-op.org/download',
    zip_safe = False,

    entry_points = {
        'bitten.recipe_commands': [
            'http://dita-op.org/tools#dita = dita_op.tools:dita'
        ]
    },
)
