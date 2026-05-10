
/**
 * Formats showtime attributes into a human-readable shorthand string
 *
 * @param {Object} showtimeObject - The showtime data from the backend.
 * @returns {String} A formatted string of active attributes (e.g. "SUB - 3D")
 */
export function connectWithDash (showtimeObject){
    /*
    Showtime Attributes
    - The keys correspond to the showtime database properties
    - The values correspond to the text rendered on the screen
    */
    const MAP = {
        'subtitles': 'SUB',
        'is_3d': '3D',
        'dubbed': 'DUB'
    };

    // Filter the MAP object to get the correct text connected with dashes
    const finalString = Object.entries(MAP)
        .filter(([key]) => showtimeObject[key])
        .map(([, value]) => value)
        .join(' - ');

    return finalString;
}

/**
 * Formats showtime information into formatted string for the select options
 * 
 * Supports movie, theater and screen select fields.
 * 
 * @param {String} field - The select field name ('movie', 'theater', or 'screen').
 * @param {Object} data - The object containing the data for the selected field.
 * @returns {String} A formatted string displayed inside the select option.
 * 
 * Examples:
 *  - Movie: "The Spongebob Movie Search For Squarepants"
 *  - Theater: "Joker, Split"
 *  - Screen: "Screen 1"
 */
export function formatSelectText (field, data) {
    /*
    Showtime Information
    - The keys correspond to the Showtime Create form input fields
    */

    const MAP = {
        movie: data.title,
        theater: `${data.name}, ${data.city}`,
        screen: `Screen ${data.label}`,
    };

    return MAP[field]
}
