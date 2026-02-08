import connectWithDash from "../../utils/showtimeHelpers";

export default function ShowtimeGrid ({showtimes, movie}) {
    return(
        <div className="grid grid-cols-6 gap-1">
            {
                showtimes.map((showtime) => {
                    // Turn the datetime string into a Date object
                    const date = new Date(showtime['start_time']);
                    if(movie.id === showtime['movie_id']){
                        return(
                            <a className={showtime['is_bookable'] 
                                ? `flex justify-center border-1 border-slate-800 py-3 px-5 transition duration-350 ease-in-out hover:border-sky-400`
                                : 'flex justify-center border-1 border-slate-800 py-3 px-5 opacity-60'}
                               key={showtime.id}
                               href={showtime['is_bookable'] ? `/showtime/${showtime.id}` : `#`}
                            >
                                {/* Display information about the showtime */}
                                <div className="flex flex-col justify-center">
                                    <div className="flex flex-row justify-center text-xs min-h-5">
                                        {/* Dynamically generate info regarding whether the movie during the showtime:
                                                - Has subtitles (SUB)
                                                - Is in 3d (3D)
                                                - Or is dubbed (DUB)
                                            Dynamically generate a dash between "SUB", "3D" and "DUB" */}
                                        <div className="opacity-70">{connectWithDash(showtime)}</div>
                                    </div>

                                    {/* Display the time in the HH-MM format */}
                                    <div className="flex justify-center text-2xl font-bold">{date.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute:'2-digit' })}</div>
                                    {/* Display the screen number of the showtime */}
                                    <div className="flex justify-center opacity-90">Screen {showtime['screen_id']}</div>
                                </div>
                            </a>
                        )
                    }
                })
            }
        </div>
    )
}