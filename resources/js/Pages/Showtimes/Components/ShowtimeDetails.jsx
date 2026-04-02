import { usePage } from "@inertiajs/react";

const attributes = {
    subtitles: 'Subtitles',
    dubbed: 'Dubbed',
    is_3d: '3D',
};

export default function ShowtimeDetails() {
    const { showtime } = usePage().props;

    // Filter showtime attributes set to TRUE
    const showtimeAttrs = Object.entries(attributes).filter(([label, value]) => showtime[label] == true);

    return(
        <div>
            <div className="flex flex-col items-center my-5">
                <div className="my-5 text-2xl font-bold">Showtime Details</div>
                <div className="my-2 text-xl font-bold">{showtime['date']}</div>
                <div>Showtime start time: <strong>{showtime['start_time']}</strong></div>
                <div>Showtime end time: <strong>{showtime['end_time']}</strong> {showtime['date'] !== showtime['end_date'] ? '(+1)' : ''}</div>
                <div>Movie: <strong>{showtime['movie']}</strong></div>
                <div>Theater: <strong>{showtime['theater']}, {showtime['city']}</strong></div>
                <div>Screen {showtime['screen']}</div>
                <div className="my-5 text-xl font-bold flex flex-col items-center">
                    Attributes
                    <ul className="text-sm font-normal list-disc">
                        {
                            showtimeAttrs.map(([label, value]) => (
                                <li key={label}>{value}</li>
                            ))
                        }
                    </ul>
                </div>
            </div>
        </div>
    )
}