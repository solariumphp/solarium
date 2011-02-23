<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

class Solarium_VersionTest extends PHPUnit_Framework_TestCase
{

    public function testVersion()
    {
        $version = Solarium_Version::VERSION;
        $this->assertNotNull($version);
    }

    public function testCheckExact()
    {
        $this->assertTrue(
            Solarium_Version::checkExact(Solarium_Version::VERSION)
        );
    }

    public function testCheckExactPartial()
    {
        $this->assertTrue(
            Solarium_Version::checkExact(substr(Solarium_Version::VERSION,0,1))
        );
    }

    public function testCheckExactLower()
    {
        $this->assertFalse(
            Solarium_Version::checkExact('0.1')
        );
    }

    public function testCheckExactHigher()
    {
        $this->assertFalse(
            Solarium_Version::checkExact('99.0')
        );
    }

    public function testCheckMinimal()
    {
        $this->assertTrue(
            Solarium_Version::checkMinimal(Solarium_Version::VERSION)
        );
    }

    public function testCheckMinimalPartial()
    {
        $version = substr(Solarium_Version::VERSION,0,1);
        
        $this->assertTrue(
            Solarium_Version::checkMinimal($version)
        );
    }

    public function testCheckMinimalLower()
    {
        $this->assertTrue(
            Solarium_Version::checkMinimal('0.1.0')
        );
    }

    public function testCheckMinimalHigher()
    {
        $this->assertFalse(
            Solarium_Version::checkMinimal('99.0')
        );
    }

}