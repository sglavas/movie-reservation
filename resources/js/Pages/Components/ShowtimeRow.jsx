import ShowtimeGrid from "./ShowtimeGrid";
import { useState, useRef} from "react";

export default function ShowtimeRow({dates, movie, showtimes}) {
    const [isOpen, setIsOpen] = useState(false);

    const heightRef = useRef(null);

    // Get the scrollHeight of the hidden elements to generate an accordion animation
    const maxHeight = isOpen ? heightRef.current?.scrollHeight + 'px' : 0


    const toggleVisibility = () => {
        setIsOpen(n => !n);
    };
    return(
        <div>
            {/* Show More Button
                - Display if there are more than 2 showtime dates for each movie.
                - Hide if otherwise 
            */}
            <button onClick={toggleVisibility} type="button" className={`bg-blue-500 hover:bg-blue-700 text-white text-xl font-bold py-2 px-4 rounded-full ${Object.values(showtimes).length > 2 ? '' : 'hidden'}`}>
                <div className="flex flex-row gap-2 items-center">
                    {/* Conditionally Rendered Plus/Minus heroicons with Show More/Show Less Text */}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth='1.5' stroke="currentColor" className="size-6">
                        <path strokeLinecap="round" strokeLinejoin="round" d={isOpen ? 'M5 12h14' : 'M12 4.5v15m7.5-7.5h-15' } />
                    </svg>
                    {isOpen ? 'Show less' : 'Show more'}
                </div>
            </button>
            {/* The Rest of Showtime Dates */}
            <div className={`transition-all duration-600 ease-in-out overflow-hidden`} style={{maxHeight: maxHeight}} ref={heightRef}>
                {
                    dates.map(([dayLabel, allShowtimesForDay]) => {
                        return(
                                <div className={`my-6`} key={dayLabel}>
                                    <div className="text-amber-300 font-bold">{dayLabel.toUpperCase()}</div>
                                    <ShowtimeGrid showtimes = {allShowtimesForDay}
                                                  movie = {movie}
                                    />
                                </div>
                            )
                    })
                }
            </div> 
        </div>
    )
}