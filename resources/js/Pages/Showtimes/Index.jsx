import Layout from "../Components/Layout"
import ShowtimeGrid from "./Components/ShowtimeGrid"
import ShowtimeRow from "./Components/ShowtimeRow";

export default function Index({movies}) {
    return(
            <div className="flex flex-col">
                {Object.values(movies).map(({movie, showtimes}) =>  {

                    // Convert the showtims into an array of key value pairs
                    const arrayShowtimes = Object.entries(showtimes);

                    // Slice the first two showtimes
                    const firstTwoDates = arrayShowtimes.slice(0, 2);

                    // Slice the rest of the showtimes
                    const otherDates = arrayShowtimes.slice(2);

                    // Render only those movies that have showtimes
                    if(Object.values(showtimes).length > 0){

                        return(
                            <div key={movie.id} 
                                className="w-full flex flex-row justify-around mb-5 px-4 py-5 border-b border-gray-100/10">
                                {/* Poster Column */}
                                <div>
                                    <img src={`/storage/posters/${movie.id}.jpg`} 
                                        className="size-60 rounded-sm" />
                                </div>
                                <div className="w-full pl-5 pb-5 block transition-all duration-900 ease-in-out">
                                    <a href={`/movie/${movie.id}`}
                                    className="font-bold text-2xl"
                                    >
                                        {movie.title}
                                    </a>
                                    {/* Capitalize first letter */}
                                    <div className="font-bold">Genre: {movie.genre.charAt(0).toUpperCase() + movie.genre.slice(1)}</div>
                                    <div className="font-bold mb-5">{movie.duration} min</div>
                                    <div>{movie.description}</div>
                                    {/* The First Two Showtime Dates */}
                                    <div>
                                        {
                                            /* Get the entries from the showtimes array, and extract the dayLabel keys and the showtimes values for a specific movie on a specific date */
                                            (Object.values(showtimes).length > 2 ? firstTwoDates : arrayShowtimes).map(([dayLabel, allShowtimesForDay]) => {
                                                return(
                                                    <div className="my-6" key={dayLabel}>
                                                        <div className="text-amber-300 font-bold">{dayLabel.toUpperCase()}</div>
                                                        <ShowtimeGrid showtimes = {allShowtimesForDay}
                                                                    movie = {movie}
                                                        />
                                                    </div>
                                                )                                    
                                            })
                                        }
                                    </div>
                                    {/* The Rest of Showtime Dates */}
                                    <ShowtimeRow  
                                        dates={otherDates}
                                        movie={movie}
                                        showtimes={showtimes}
                                    />
                                </div>
                            </div>
                        )

                    }

                })}
            </div>
    )
}

Index.layout = page => <Layout children={page} slot="Showtime Page" />