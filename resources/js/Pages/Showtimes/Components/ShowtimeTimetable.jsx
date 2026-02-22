import { Link, usePage } from "@inertiajs/react"

export default function ShowtimeTimetable () {
    const { timetables } = usePage().props;

    return(
        <div className="border-b border-white/10 pb-12">
                {
                    // Object.entries(showtimesOrderedbyScreen).map(([screenLabel, allShowtimesForScreen]) => (
                    timetables.map((timetable) => (
                        <div key={timetable['screen_id']}>
                            {/* Screens */}
                            <div className="text-xl font-bold my-5">{timetable['screen_name']}</div>
                            {/* <div className="text-xl font-bold my-5">Screen {screenLabel}</div> */}
                            <div className="grid grid-cols-4 gap-4 ">
                                {/* Showtimes belonging to a certain screen */}
                                {
                                    timetable.showtimes.map((showtime) => (
                                        <Link href={`/showtimes/${showtime.id}`} key={showtime.id} className="border-1 border-slate-800 py-3 px-5 transition duration-350 ease-in-out hover:border-sky-400">
                                            <div className="flex flex-col justify-start items-start">
                                                <div className="mb-5 text-lg font-bold">{showtime['movie_title']}</div>
                                                <div className="flex flex-col">
                                                    <div>Date: <strong>{showtime['date']}</strong></div>
                                                    <div>Start time: {showtime['start_time']} </div>
                                                    {/* If the movie starts before midnight and ends on the following day, add (+1) */}
                                                    <div>End time: {showtime['end_time']} {showtime['date'] !== showtime['end_date'] ? '(+1)' : ''}</div>
                                                </div>
                                            </div>
                                        </Link>
                                    ))
                                }
                            </div>
                        </div>
                    ))
                }
        </div>
    )
}