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
		style,
		aggregate,
		reviews,
		pagination,
		location,
		columns
	} = attributes;

	const { locations } = window.odrfReviewsBlockConfig;

	const locationOptions =
		Object.entries(locations)
			.map( ([key, {name}]) => ({
				label: name,
				value: key
			}) );

	const wrapperClasses = () => [ "odrf-reviews", `odrf-style-${style||"default"}` ].join(" ");

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title="Reviews Display Options">
					<PanelRow>
						<SelectControl
							label="Style"
							value={ style }
							options={ [
								{ label: 'Plain', value: '' },
								{ label: 'Boxed', value: 'boxed' },
								{ label: 'Quote', value: 'quote' }
							] }
							onChange={ ( style ) => setAttributes( { style: style || "" } ) }
						/>
					</PanelRow>
					{locationOptions.length > 1 ? <PanelRow>
						<SelectControl
							label="Location"
							value={ location }
							options={ [ { label: "All", value: "" }, ...locationOptions ] }
							onChange={ ( location ) => setAttributes( { location: location || "" } ) }
						/>
					</PanelRow> : null}
					<PanelRow>
						<ToggleControl
							label="Display Aggregate Header?"
							checked={ aggregate }
							onChange={ ( aggregate ) => setAttributes( { aggregate } ) }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label="Number of Reviews"
							value={ reviews }
							type="number"
							onChange={ ( reviews ) => setAttributes( { reviews: reviews ? parseInt(reviews): reviews } ) }
							min={ 0 }
							max={ 20 }
						/>
					</PanelRow>
					{reviews ? <PanelRow>
						<RangeControl
							label="Columns"
							value={ columns }
							onChange={ ( columns ) => setAttributes( { columns } ) }
							min={ 1 }
							max={ 3 }
						/>
					</PanelRow> : null}
					{reviews ? <PanelRow>
						<ToggleControl
							label="Display Pagination?"
							checked={ pagination }
							onChange={ ( pagination ) => setAttributes( { pagination } ) }
						/>
					</PanelRow> : null}
				</PanelBody>
			</InspectorControls>
			<div className={wrapperClasses()}>
				<div className="odrf-aggregate-wrap" style={{textAlign:"center"}}>
					<span className="odrf-aggregate-name">Odd Dog Reviews</span>
					<Stars />
				</div>
			</div>
		</div>
	);

}
