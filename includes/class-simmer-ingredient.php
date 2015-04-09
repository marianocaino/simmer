<?php
/**
 * Define the single ingredient class
 * 
 * @since 1.0.0
 * 
 * @package Simmer/Ingredients
 */

// If this file is called directly, get outa' town.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class for gathering and formatting information about a single recipe ingredient.
 * 
 * @since 1.0.0
 */
final class Simmer_Ingredient {
	
	/**
	 * The ingredient amount.
	 * 
	 * @since 1.0.0
	 * 
	 * @var string $amount
	 */
	public $amount = '';
	
	/**
	 * The ingredient unit of measure.
	 * 
	 * @since 1.0.0
	 * 
	 * @var string $unit
	 */
	public $unit = '';
	
	/**
	 * The ingredient description.
	 * 
	 * @since 1.0.0
	 * 
	 * @var string $description
	 */
	public $description = '';
	
	/**
	 * Construct the ingredient object.
	 * 
	 * @since 1.0.0
	 * 
	 * @param array $ingredient The single ingredient's data from post meta.
	 * @param bool  $raw        Optional. Whether the ingredient object should be raw or formatted for output.
	 */
	public function __construct( $ingredient, $filter = 'display' ) {
		
		if ( isset( $ingredient['amt'] ) ) {
			
			$raw_amount = $ingredient['amt'];
			
			if ( 'raw' == $filter ) {
				$amount = $raw_amount;
			} else {
				$amount = $this->convert_amount_to_string( $raw_amount );
			}
			
			$this->amount = $amount;
		}
		
		if ( isset( $ingredient['unit'] ) ) {
			
			$raw_unit = $ingredient['unit'];
			
			if ( 'raw' == $filter ) {
				$unit = $raw_unit;
			} else {
				$unit = $this->get_unit_label( $raw_unit, $raw_amount );
			}
			
			$this->unit = $unit;
		}
		
		if ( isset( $ingredient['desc'] ) ) {
			$this->description = $ingredient['desc'];
		}
	}
	
	/**
	 * Convert an ingredient amount float to a string.
	 *
	 * @since  1.0.0
	 * @access private
	 
	 * @param  float      $amount The amount to convert.
	 * @return string|int $amount The converted amount.
	 */
	private function convert_amount_to_string( $amount ) {
		
		if ( ! is_numeric( $amount ) ) {
			return false;
		}
		
		$whole = floor( $amount );
		
		$decimal = $amount - $whole;
		
		$least_common_denominator = 48; // 16 * 3;
		
		$denominators = array( 2, 3, 4, 8, 16, 24, 48 );
		
		$decimal = round( $decimal * $least_common_denominator ) / $least_common_denominator;
		
		if ( 0 == $decimal ) {
			
			$amount = $whole;
			
		} else if ( 1 == $decimal ) {
			
			$amount = $whole + 1;
			
		} else {
			
			foreach ( $denominators as $denominator ) {
				
				if ( $decimal * $denominator == floor ( $decimal * $denominator ) ) {
					break;
				}
			}
			
			$amount = ( 0 == $whole ? '' : $whole . ' ' ) . ( $decimal * $denominator ) . '/' . $denominator;
			
		}
		
		return $amount;
	}
	
	/**
	 * Convert the amount string to a float.
	 *
	 * @since 1.0.0
	 * 
	 * @param  string|int $amount The amount to convert.
	 * @return float      $amount The converted amount.
	 */
	public static function convert_amount_to_float( $amount ) {
		
		// Assume there is no whole number.
		$has_whole = false;
		
		// Remove whitespace.
		$amount = trim( $amount );
		
		// Check for a space, signifying a whole number with a fraction.
		if ( strstr( $amount, ' ' ) ) {
			
			$reversed     = strrev( $amount );
			$whole_number = strrev( strstr( $reversed, ' ' ) );
			
			$has_whole = true;
		}
		
		// Now check the fraction part.
		if ( strstr( $amount, '/' ) ) {
			
			// Isolate the fraction.
			if ( true == $has_whole ) {
				$amount = strstr( $amount, ' ' );
			}
			
			$divisor = str_replace( '/', '', strstr( $amount, '/' ) );
			
			// Isolate the numerator.
			$numerator = strrev( $amount );
			$numerator = strstr( $numerator, '/' );
			$numerator = strrev( $numerator );
			$numerator = str_replace( '/', '', $numerator );
			
			if ( true == $has_whole ) {
				$numerator = $numerator + ( $whole_number * $divisor );   
			}
			
			$amount = $numerator / $divisor;
		}
		
		return $amount;
	}
	
	/**
	 * Get the approprate label for a given unit based on count.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $unit  The given unit & its labels.
	 * @param  int    $count Optional. The ingredient count.
	 * @return string $label The appropriate label.
	 */
	public static function get_unit_label( $unit, $count = 1 ) {
		
		if ( ! is_array( $unit ) ) {
			
			$units = Simmer_Ingredients::get_units();
			
			foreach( $units as $type => $units ) {
				if ( isset( $units[ $unit ] ) ) {
					
					$unit = $units[ $unit ];
					break;
				}
			}
		}
		
		// If an abbreviation is set, use that.
		if ( 'abbr' == get_option( 'simmer_units_format' ) && isset( $unit['abbr'] ) && ! empty( $unit['abbr'] ) ) {
			
			$label = $unit['abbr'];
			
		// Otherwise, choose either plural or single based on the count.
		} else if ( isset( $unit['plural'] ) && 1 < (float) $count ) {
			
			$label = $unit['plural'];
			
		} else if ( isset( $unit['single'] ) ) {
			
			$label = $unit['single'];
			
		// Finally, if none of the appropriate labels are set then bail.
		} else {
			return false;
		}
		
		$label = apply_filters( 'simmer_get_unit_label', $label, $unit, $count );
		
		return $label;
	}
}
