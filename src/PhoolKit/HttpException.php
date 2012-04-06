<?php
/*
 * PhoolKit - A PHP toolkit.
 * Copyright (C) 2012  Klaus Reimer <k@ailis.de>
 *
 * This library is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at
 * your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhoolKit;

use RuntimeException;

/**
 * This interface can be used to mark an exception type as an HTTP exception.
 * HTTP exceptions must have an exception code which matches the corresponding
 * HTTP status code (404 for errors indicating a missing resource for example).
 *
 * @author Klaus Reimer (k@ailis.de)
 */
interface HttpException {}
