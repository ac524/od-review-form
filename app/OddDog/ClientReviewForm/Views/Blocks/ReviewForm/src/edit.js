/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';

import {
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	TextControl,
	RangeControl
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

function Star() {
	return <span
			className="odrf-star"
			><svg
				xmlns="http://www.w3.org/2000/svg"
				width="24"
				height="24"
				viewBox="0 0 24 24"
				className="odrf-star-filled"><path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z"/></svg></span>
}

function Stars( { rating = 5 } ) {

	const styles = {};
	const ceil = Math.ceil( rating );

	if( rating !== ceil ) {
		styles.marginRight = ((1 - (rating/ceil))*100) + "%";
	}

	const stars = [];

	for( let i = 0; i<rating; i++ ) {
		stars.push(<Star key={i}/>);
	}

	return (
		<span className="odrf-stars-wrap"><span className="odrf-stars" style={styles}>{stars}</span></span>
	);

}

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {

	const {
		location
	} = attributes;

	const { locations } = window.odrfReviewFormBlockConfig;

	const locationOptions =
		Object.entries(locations)
			.map( ([key, {name}]) => ({
				label: name,
				value: key
			}) );

	const wrapperClasses = () => [ "odrf-reviews" ].join(" ");

	return (
		<div { ...useBlockProps() }>
			{locationOptions.length > 1 ? <InspectorControls>
				<PanelBody title="Review Form Options">
					<PanelRow>
						<SelectControl
							label="Location"
							value={ location }
							options={ [ { label: "Any", value: "" }, ...locationOptions ] }
							onChange={ ( location ) => setAttributes( { location: location || "" } ) }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls> : null}
			<div className={wrapperClasses()}>
				<div className="odrf-aggregate-wrap" style={{textAlign:"center"}}>
					<span className="odrf-aggregate-name">Odd Dog Review Form</span>
					<Stars />
				</div>
			</div>
		</div>
	);

}
