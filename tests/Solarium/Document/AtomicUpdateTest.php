<?php

class AutomaticUpdateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Solarium_Document_AtomicUpdate::addField
	 * @covers Solarium_Document_AtomicUpdate::getModifierForField
	 * @covers Solarium_Document_AtomicUpdate::setModifierForField
	 */
	public function testAtomicUpdateStoresModifier() {
		
		$doc = new Solarium_Document_AtomicUpdate();
		$doc->addField( 'foo', 'bar' );
		
		$modifiers = new ReflectionProperty( 'Solarium_Document_AtomicUpdate', '_modifiers' );
		$modifiers->setAccessible( true );
		$modifierArray = $modifiers->getValue( $doc );
		
		$this->assertEquals(
				Solarium_Document_AtomicUpdate::MODIFIER_SET,
				$modifierArray['foo'],
				'Solarium_Document_AtomicUpdate should set any added field to modify as a setter by default'
		);
		
		$this->assertNull(
				$doc->getModifierForField( 'baz' ),
				'Solarium_Document_AtomicUpdate::getModifierForField should return the modifier string for the field provided, if not set'
		);
		
		try {
			$doc->addField('test', 'breaks', null, 'not a real modifier');
		} catch ( Exception $e ) { }
		
		$this->assertInstanceOf(
				'Exception',
				$e,
				'Solarium_Document_AtomicUpdate::setModifierForField should throw an exception if not passed a legal modifier'
		);
		
		$doc->addField( 'baz', 'qux', null, Solarium_Document_AtomicUpdate::MODIFIER_ADD );

		$this->assertEquals(
				Solarium_Document_AtomicUpdate::MODIFIER_ADD,
				$doc->getModifierForField( 'baz' ),
				'Solarium_Document_AtomicUpdate::getModifierForField should return the modifier string for the field provided, if set'
		);
	}
	
	/**
	 * @covers Solarium_Document_AtomicUpdate::setKey
	 * @covers Solarium_Document_AtomicUpdate::getFields
	 */
	public function testAtomicUpdateStoresKey() {
		
		$doc = new Solarium_Document_AtomicUpdate();
		
		try {
			$doc->getFields();
		} catch ( Exception $e1 ) { }
		
		$this->assertInstanceOf(
				'Exception',
				$e1,
				'Solarium_Document_AtomicUpdate should throw an exception if getFields() is called before a key is set to prevent malformed requests'
		);
		
		$doc->setKey('id', '123_456');
		
		$e2 = null;
		try {
			$doc->getFields();
		} catch ( Exception $e2 ) { }
		
		$this->assertNull(
				$e2,
				'Solarium_Document_AtomicUpdate should not throw an exception if getFields() is called after a key is set'
		);
		
		$modifiers = new ReflectionProperty( 'Solarium_Document_AtomicUpdate', '_modifiers' );
		$modifiers->setAccessible( true );
		$modifierArray = $modifiers->getValue( $doc );
		
		$this->assertEmpty(
				$modifierArray,
				'Solarium_Document_AtomicUpdate::setKey should not store a modifier for the unique key'
		);
		
		$keyRefl = new ReflectionProperty( 'Solarium_Document_AtomicUpdate', 'key' );
		$keyRefl->setAccessible( true );
		
		$this->assertEquals(
				'id',
				$keyRefl->getValue( $doc ),
				'Solarium_Document_AtomicUpdate::setKey should set the "key" member variable to the name of the field'
		);
	}

}