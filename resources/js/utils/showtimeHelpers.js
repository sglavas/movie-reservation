
/**
 * Formats showtime attributes into a human-readable shorthand string
 *
 * @param {Object} showtimeObject - The showtime data from the backend.
 * @returns {String} A formatted string of active attributes (e.g. "SUB - 3D")
 */
const connectWithDash = (showtimeObject) => {
    /*
    Showtime Attributes
    - The keys correspond to the showtime database propertires
    - The values correspond to the text rendered on the screen
    */
    const MAP = {
        'subtitles': 'SUB',
        '3d': '3D',
        'dubbed': 'DUB'
    };

    // Filter the MAP object to get the correct text connected with dashes
    const finalString = Object.entries(MAP)
        .filter(([key]) => showtimeObject[key])
        .map(([, value]) => value)
        .join(' - ');

    return finalString;
}

export default connectWithDash;
